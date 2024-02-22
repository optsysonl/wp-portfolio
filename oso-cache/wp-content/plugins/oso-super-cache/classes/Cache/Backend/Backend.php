<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Backend;

use OSOSuperCache\Factory;

class Backend
{
    private static $instance;

    public $templatePath;

    private $messages = [];

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public function __construct()
    {
        /* Load textdomain */
        add_action('plugins_loaded', [$this, 'loadTextdomain']);

        /* Meta Box */
        add_action('wp_loaded', [$this, 'handleMetaBox']);

        /* Show message if plugin was activated/deactivated or theme was switched */
        add_action('activated_plugin', [$this, 'handleSystemChanged']);
        add_action('deactivated_plugin', [$this, 'handleSystemChanged']);
        add_action('switch_theme', [$this, 'handleSystemChanged']);

        /* Show activated successfully message and inform user to activate caching */
        add_action('admin_notices', [$this, 'successfullyActivatedMessage']);

        /* Show message when a plugin or theme was changed */
        add_action('admin_notices', [$this, 'systemChangedMessage']);

        /* Register handler for AJAX requests like dismissed notices */
        add_action('wp_ajax_oso_super_cache_handler', [$this, 'handleAjaxRequest']);

        /* Add menu */
        add_action('admin_menu', [$this, 'addMenu']);

        /* Add toolbar menu */
        add_action('wp_before_admin_bar_render', [$this, 'addToolbarMenu']);

        /* Load JavaScript & CSS */
        add_action('admin_enqueue_scripts', [$this, 'registerAdminScripts']);

        /* Post was moved to trash */
        add_action('wp_trash_post', [Factory::get('Cache\Frontend\Garbage'), 'removeFromCache']);

        $this->templatePath = realpath(dirname(__FILE__).'/../../../templates');
    }

    /**
     * handleAjaxRequest function.
     *
     * @access public
     * @return void
     */
    public function handleAjaxRequest()
    {
        if (!empty($_POST['type'])) {
            $requestType = $_POST["type"];

            if (check_ajax_referer('oso-super-cache-dismissed-notice', false, false)) {
                if ($requestType == 'osoSuperCacheSystemChangedMessage') {
                    update_option('OSOSuperCacheSystemChangedMessage', false, 'yes');
                } elseif ($requestType == 'osoSuperCacheSystemActivatedMessage') {
                    update_option('OSOSuperCacheActivatedMessage', true, 'yes');
                }
            } elseif (check_ajax_referer('oso-super-cache-maintenance-refresh-cache', false, false)) {
                if ($requestType == 'osoSuperCacheRefreshCache') {
                    Factory::get('Cache\Frontend\Garbage')->clearCache();

                    echo json_encode([
                        'message'=>Factory::get('Cache\Backend\Dashboard')->getCacheMaintenanceMessage('refresh'),
                    ]);
                }
            } elseif (check_ajax_referer('oso-super-cache-maintenance-clear-styles-pre-cache', false, false)) {
                if ($requestType == 'osoSuperCacheClearStylesPreCache') {
                    Factory::get('Cache\Frontend\Garbage')->clearStylesPreCacheFiles();

                    echo json_encode([
                        'message'=>Factory::get('Cache\Backend\Dashboard')->getCacheMaintenanceMessage('clearStylesPreCache'),
                    ]);
                }
            } elseif (check_ajax_referer('oso-super-cache-indexing', false, false)) {
                if ($requestType == 'osoSuperCacheIndexing') {
                    echo json_encode(Factory::get('Cache\Backend\CacheIndexing')->handleIndexingProcess($_POST));
                }
            }
        }

        wp_die();
    }

    /**
     * loadTextdomain function.
     *
     * @access public
     * @return void
     */
    public function loadTextdomain()
    {
        load_plugin_textdomain('oso-super-cache', false, dirname(OSO_SUPER_CACHE_SLUG).'/languages/');
    }

    /**
     * handleMetaBox function.
     *
     * @access public
     * @return void
     */
    public function handleMetaBox()
    {
        global $user;

        $user = wp_get_current_user();

        //! TODO: Add support for custom roles
        if (!empty($user->allcaps['publish_posts'])) {

            /* Meta Box */
            if (Factory::get('Cache\Config')->get('cacheShowMetaBox')) {
                add_action('add_meta_boxes', [Factory::get('Cache\Backend\MetaBox'), 'registerMetaBox']);
                add_action('save_post', [Factory::get('Cache\Backend\MetaBox'), 'saveMetaBoxData'], 10, 3);
            }

            /* Column and QuickEdit */
            if (Factory::get('Cache\Config')->get('cacheShowRefreshOptionInQuickEdit')) {

                $postTypes = AdvancedSettings::getInstance()->getPostTypes();

                if (!empty($postTypes)) {
                    foreach ($postTypes as $postTypeData) {
                        add_action('manage_'.$postTypeData->name.'_posts_columns', [Factory::get('Cache\Backend\MetaBox'), 'addPostColumn']);
                        add_action('manage_'.$postTypeData->name.'_posts_custom_column', [Factory::get('Cache\Backend\MetaBox'), 'handlePostColumn'], 10, 2);
                    }
                }

                add_action('quick_edit_custom_box', [Factory::get('Cache\Backend\MetaBox'), 'displayQuickEditOption'], 10, 2);
            }
        }
    }

    /**
     * addMenu function.
     *
     * @access public
     * @return void
     */
    public function addMenu()
    {
        /* Main menu */
        add_menu_page(
            _x('OSO Super Cache', 'Site title', 'oso-super-cache'),
            _x('OSO Super Cache', 'Menu entry', 'oso-super-cache'),
            'administrator', /* lowest administrator level */
            'oso-super-cache',
            [Factory::get('Cache\Backend\View'), 'display__Dashboard'],
            Factory::get('Cache\Backend\Icons')->getAdminSVGIcon(),
            null /* menu position */
        );

        /* Dashboard */
        add_submenu_page(
            'oso-super-cache',
            _x('Dashboard Settings', 'Site title', 'oso-super-cache'),
            _x('Dashboard', 'Menu entry', 'oso-super-cache'),
            'administrator',
            'oso-super-cache',
            [Factory::get('Cache\Backend\View'), 'display__Dashboard']
        );

        /* Advanced Settings */
        add_submenu_page(
            'oso-super-cache',
            _x('Advanced Caching Settings', 'Site title', 'oso-super-cache'),
            _x('Advanced Settings', 'Menu entry', 'oso-super-cache'),
            'administrator',
            'oso-super-cache-advanced-settings',
            [Factory::get('Cache\Backend\View'), 'display__AdvancedSettings']
        );

        /* CDN */
        add_submenu_page(
            'oso-super-cache',
            _x('CDN', 'Site title', 'oso-super-cache'),
            _x('CDN', 'Menu entry', 'oso-super-cache'),
            'administrator',
            'oso-super-cache-cdn',
            [Factory::get('Cache\Backend\View'), 'display__CDNSettings']
        );

        /* Cache Indexing */
        add_submenu_page(
            'oso-super-cache',
            _x('Cache Indexing', 'Site title', 'oso-super-cache'),
            _x('Cache Indexing', 'Menu entry', 'oso-super-cache'),
            'administrator',
            'oso-super-cache-indexing',
            [Factory::get('Cache\Backend\View'), 'display__CacheIndexing']
        );

        /* View Cache */
        add_submenu_page(
            'oso-super-cache',
            _x('View Cache', 'Site title', 'oso-super-cache'),
            _x('View Cache', 'Menu entry', 'oso-super-cache'),
            'administrator',
            'oso-super-cache-view-cache',
            [Factory::get('Cache\Backend\View'), 'display__ViewCache']
        );

        /* Optimize Database */
        add_submenu_page(
            'oso-super-cache',
            _x('Optimize Database', 'Site title', 'oso-super-cache'),
            _x('Optimize Database', 'Menu entry', 'oso-super-cache'),
            'administrator',
            'oso-super-cache-optimize-database',
            [Factory::get('Cache\Backend\View'), 'display__OptimizeDatabase']
        );

        /* Fragments */
        add_submenu_page(
            'oso-super-cache',
            _x('Fragments', 'Site title', 'oso-super-cache'),
            _x('Fragments', 'Menu entry', 'oso-super-cache'),
            'administrator',
            'oso-super-cache-fragments',
            [Factory::get('Cache\Backend\View'), 'display__Fragments']
        );

        /* Import Export */
        add_submenu_page(
            'oso-super-cache',
            _x('Import / Export Settings', 'Site title', 'oso-super-cache'),
            _x('Import &amp; Export', 'Menu entry', 'oso-super-cache'),
            'administrator',
            'oso-super-cache-import-export',
            [Factory::get('Cache\Backend\View'), 'display__ImportExport']
        );

        /* License */
//        add_submenu_page(
//            'oso-super-cache',
//            _x('License', 'Site title', 'oso-super-cache'),
//            _x('License', 'Menu entry', 'oso-super-cache'),
//            'administrator',
//            'oso-super-cache-license',
//            [Factory::get('Cache\Backend\View'), 'display__License']
//        );

        /* About */
//        add_submenu_page(
//            'oso-super-cache',
//            _x('About OSO Super Cache', 'Site title', 'oso-super-cache'),
//            _x('About', 'Menu entry', 'oso-super-cache'),
//            'administrator',
//            'oso-super-cache-about',
//            [Factory::get('Cache\Backend\View'), 'display__About']
//        );
    }

    /**
     * addToolbarMenu function.
     *
     * @access public
     * @return void
     */
    public function addToolbarMenu()
    {
        global $wp_admin_bar;

        if (Factory::get('Cache\Config')->get('miscellaneousDisableOSOSuperCacheToolbarMenuItem') == false) {
            $user = wp_get_current_user();

            if (!empty($user->allcaps['publish_posts'])) {
                /* Main Toolbar Menu */
                $wp_admin_bar->add_node([
                    'id'=>'oso-super-cache',
                    'title'=>_x('OSO Super Cache', 'Menu entry', 'oso-super-cache'),
                ]);

                /* Refresh Cache Menu Item */
                $message = _x('Do you really want to refresh the complete cache?', 'Message', 'oso-super-cache');
                $nonce = wp_create_nonce('oso-super-cache-maintenance-refresh-cache');
                $type = 'osoSuperCacheRefreshCache';

                $wp_admin_bar->add_node([
                    'id'=>'oso-super-cache-refresh',
                    'title'=>_x('Refresh Cache', 'Menu entry', 'oso-super-cache'),
                    'parent'=>'oso-super-cache',
                    'href'=>'#',
                    'meta'=>[
                        'class'=>'oso-super-cache-toolbar-cache-maintenance',
                        'html'=>'<span data-oso-super-cache-confirm-msg="'.$message.'" data-oso-super-cache-nonce="'.$nonce.'" data-oso-super-cache-maintenance-type="'.$type.'"></span>',
                    ],
                ]);

                /* Clear CSS-Pre-Cache Files Menu Item */
                $message = _x('Do you really want to clear the CSS pre-cache files?', 'Message', 'oso-super-cache');
                $nonce = wp_create_nonce('oso-super-cache-maintenance-clear-styles-pre-cache');
                $type = 'osoSuperCacheClearStylesPreCache';

                $wp_admin_bar->add_node([
                    'id'=>'oso-super-cache-clear-styles-pre-cache',
                    'title'=>_x('Clear CSS pre-cache files', 'Menu entry', 'oso-super-cache'),
                    'parent'=>'oso-super-cache',
                    'href'=>'#',
                    'meta'=>[
                        'class'=>'oso-super-cache-toolbar-cache-maintenance',
                        'html'=>'<span data-oso-super-cache-confirm-msg="'.$message.'" data-oso-super-cache-nonce="'.$nonce.'" data-oso-super-cache-maintenance-type="'.$type.'"></span>',
                    ],
                ]);
            }
        }
    }

    /**
     * registerAdminScripts function.
     *
     * @access public
     * @return void
     */
    public function registerAdminScripts()
    {
        wp_enqueue_script('oso_super_cache_admin_js', plugins_url('javascript/oso-super-cache-admin.min.js', realpath(__DIR__.'/../../')));
        wp_enqueue_style('oso_super_cache_admin_css', plugins_url('css/admin.css', realpath(__DIR__.'/../../')), [],'1.2');
    }


    /**
     * handleSystemChanged function.
     *
     * @access public
     * @return void
     */
    public function handleSystemChanged()
    {
        if (Factory::get('Cache\Config')->get('miscellaneousDisableOSOSuperCacheRefreshCacheNotice') == false) {
            // Only display system changed message when the system was not just installed and activated
            $activatedMessage = get_option('OSOSuperCacheActivatedMessage', false);

            if ($activatedMessage == true) {
                update_option('OSOSuperCacheSystemChangedMessage', true, 'yes');
            }
        }
    }

    /**
     * getMessages function.
     *
     * @access public
     * @return void
     */
    public function getMessages()
    {
        if (Factory::get('Cache\Config')->getConfigChangedStatus() == true) {
            $this->messages[] = '<p class="notice">'._x('You have changed the configuration, but these settings are not active yet.<br>If you are done with your configuration, click the button below to apply your changes.', 'Status message', 'oso-super-cache').'</p>';
            $this->messages[] = '<form method="post"><p class="align-center"><label><input type="checkbox" name="cacheMaintenanceRefresh" value="1"> '._x('Mark all pages to refresh their cache', 'Status message', 'oso-super-cache').'</label></p><p class="action-buttons align-center"><input class="button-primary" type="submit" name="applyChanges" value="'.esc_attr_x('Apply changes', 'Button title', 'oso-super-cache').'"> <input class="button-secondary" type="submit" name="resetInactiveConfig" value="'.esc_attr_x('Undo all changes', 'Button title', 'oso-super-cache').'"></p></form>';
        }

        return implode("\n", $this->messages);
    }

    /**
     * addMessage function.
     *
     * @access public
     * @param mixed $message
     * @param mixed $type (critical, offer, error, info, notice, success)
     * @return void
     */
    public function addMessage($message, $type)
    {
        $this->messages[] = '<p class="'.\esc_attr($type).'">'.$message.'</p>';
    }

    /**
     * successfullyActivatedMessage function.
     *
     * @access public
     * @return void
     */
    public function successfullyActivatedMessage()
    {
        if (get_option('OSOSuperCacheActivatedMessage', false) == false) {
        ?>
        <div class="notice notice-info is-dismissible" data-oso-super-cache-notice="osoSuperCacheSystemActivatedMessage" data-oso-super-cache-nonce="<?php echo wp_create_nonce('oso-super-cache-dismissed-notice'); ?>">
            <p><?php _ex('OSO Super Cache was successfully activated. Please <a href="admin.php?page=oso-super-cache">click here</a> and activate caching.', 'Status message', 'oso-super-cache'); ?></p>
        </div>
        <?php
        }
    }

    /**
     * systemChangedMessage function.
     *
     * @access public
     * @return void
     */
    public function systemChangedMessage()
    {
        if (get_option('OSOSuperCacheSystemChangedMessage', false) == true) {
        ?>
        <div class="notice notice-info is-dismissible" data-oso-super-cache-notice="osoSuperCacheSystemChangedMessage" data-oso-super-cache-nonce="<?php echo wp_create_nonce('oso-super-cache-dismissed-notice'); ?>">
            <p><?php _ex('The status of a plugin or a theme was changed. Please refresh the cache of OSO Super Cache to ensure it operates as intended. <a href="admin.php?page=oso-super-cache">Click here</a> and scroll down to Cache Maintenance to refresh the cache.', 'Status message', 'oso-super-cache'); ?></p>
        </div>
        <?php
        }
    }
}
?>
