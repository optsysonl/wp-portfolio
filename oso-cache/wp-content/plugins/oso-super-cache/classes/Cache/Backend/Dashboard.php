<?php
/*
 *
 * 
 */

namespace OSOSuperCache\Cache\Backend;

use OSOSuperCache\Factory;

class Dashboard
{

    private static $instance;

    private $imagePath;
    private $chartJS;

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

    protected function __construct()
    {
    }

    /**
     * display function.
     *
     * @access public
     * @return void
     */
    public function display()
    {
        global $wpdb;

        $this->imagePath = plugins_url('images', realpath(__DIR__.'/../../'));
        $this->chartJS = plugins_url('vendor/chartjs/Chart.min.js', realpath(__DIR__.'/../../'));

        if (!file_exists(Factory::get('Cache\Backend\Backend')->templatePath.'/.htaccess')) {
            file_put_contents(Factory::get('Cache\Backend\Backend')->templatePath.'/.htaccess', "<Files *.html>\nOrder Deny,Allow\nDeny from all\n</Files>");
        }

        // Check file permissions
        $htaccessPath = ABSPATH.'.htaccess';

        if (defined('OSO_SUPER_CACHE_HTACCESS_PATH')) {
            $htaccessPath = OSO_SUPER_CACHE_HTACCESS_PATH.'.htaccess';
        }

        if (!is_writable($htaccessPath) && Factory::get('Cache\Config')->get('miscellaneousNginx') == false) {
            Factory::get('Cache\Backend\Backend')->addMessage(_x('The file <strong>.htaccess</strong> is not writable. Please set the right permissions.', 'Status message', 'oso-super-cache'), 'error');
        }

        // Check if cache folder exists
        if (!file_exists(WP_CONTENT_DIR.'/cache/')) {
            if (!is_writable(WP_CONTENT_DIR)) {
                Factory::get('Cache\Backend\Backend')->addMessage(sprintf(_x('The folder <strong>/%s/</strong> is not writable. Please set the right permissions.', 'Status message', 'oso-super-cache'), basename(WP_CONTENT_DIR)), 'error');
            } else {
                mkdir(WP_CONTENT_DIR.'/cache/');
            }
        }

        if (file_exists(WP_CONTENT_DIR.'/cache/') && !is_writable(WP_CONTENT_DIR.'/cache/')) {
            Factory::get('Cache\Backend\Backend')->addMessage(sprintf(_x('The folder <strong>/%s/cache/</strong> is not writable. Please set the right permissions.', 'Status message', 'oso-super-cache'), basename(WP_CONTENT_DIR)), 'error');
        }

        // Chech if .htaccess is placed in cache folder
        if (file_exists(WP_CONTENT_DIR.'/cache/.htaccess')) {
            Factory::get('Cache\Backend\Backend')->addMessage(sprintf(_x('The folder <strong>/%s/cache/</strong> contains a <strong>.htaccess</strong> file which can prevent that visitors can load your websites CSS and JavaScript.', 'Status message', 'oso-super-cache'), basename(WP_CONTENT_DIR)), 'error');
        }

        // Chech if memory_limit is sufficient
        $memoryLimit = ini_get('memory_limit');

        if ($memoryLimit !== '-1') {
            $memoryLimitParts = [];
            preg_match('/^(\d+)(.)$/', $memoryLimit, $memoryLimitParts);

            if ($memoryLimitParts[2] == 'K') {
                $memoryLimitParts[1] = $memoryLimitParts[1] / 1024;
            }

            if ($memoryLimitParts[2] == 'G') {
                $memoryLimitParts[1] = $memoryLimitParts[1] * 1024;
            }

            if ($memoryLimitParts[1] < 128) {
                Factory::get('Cache\Backend\Backend')->addMessage(sprintf(_x('Your PHP <strong>memory_limit</strong> is too low. Please increase your current limit of %s to at least 128M. If you have a lot of plugins installed, you should increase your limit to 256M or more.', 'Status message', 'oso-super-cache'), $memoryLimitParts[1].$memoryLimitParts[2]), 'error');
            }
        }

        if (ini_get('allow_url_fopen') != true) {
            Factory::get('Cache\Backend\Backend')->addMessage(_x('Your PHP setting <strong>allow_url_fopen</strong> is <strong>Off</strong>. Please switch it <strong>On</strong> otherwise, OSO Super Cache can not merge dynamic CSS.', 'Status message', 'oso-super-cache'), 'error');
        }

        if (!empty($_POST['formSend']) && check_admin_referer('oso_super_cache_dashboard_setup')) {
            $this->saveSettings($_POST);

            Factory::get('Cache\Backend\Backend')->addMessage(_x('Saved successfully.', 'Status message', 'oso-super-cache'), 'success');
        }

        if (!empty($_POST['formSendCacheMaintenance']) && check_admin_referer('oso_super_cache_dashboard_maintenance')) {
            $this->handleCacheMaintenance($_POST);
        }

//        if (!empty($_POST['formSendCheckForUpdates']) && check_admin_referer('oso_super_cache_dashboard_check_for_updates')) {
//            $this->handleCheckForUpdates();
//        }

        $checkboxCacheActivated             = Factory::get('Cache\Config')->get('cacheActivated', 'inactive') == 'yes' ? ' checked' : '';

        $currentPreset = Factory::get('Cache\Config')->getCurrentPreset();

        $optionCachePresetCustom            = $currentPreset === 'custom' ? ' selected' : '';
        $optionCachePresetDefault           = $currentPreset === 'default' ? ' selected' : '';
        $optionCachePresetOnlyPages         = $currentPreset === 'only-pages' ? ' selected' : '';
        $optionCachePresetEcommerce         = $currentPreset === 'ecommerce' ? ' selected' : '';
        $optionCachePresetMagazine          = $currentPreset === 'magazine' ? ' selected' : '';
        $optionCachePresetCorporate         = $currentPreset === 'corporate' ? ' selected' : '';
        $optionCachePresetTestCSS           = $currentPreset === 'test-css' ? ' selected' : '';
        $optionCachePresetTestJS            = $currentPreset === 'test-js' ? ' selected' : '';

        // Statistics
        $preloadedStatsTotalPerDay = [];
        $preloadedStats = get_option('OSOSuperCachePreloadedStats', false);

        if (!empty($preloadedStats)) {
            foreach ($preloadedStats as $date => $hours) {
                $preloadedStatsTotalPerDay[$date] = 0;

                foreach ($hours as $preloads) {
                    $preloadedStatsTotalPerDay[$date] += $preloads;
                }
            }

            ksort($preloadedStatsTotalPerDay);

            $chartLabels = [];
            $chartValues = [];

            foreach ($preloadedStatsTotalPerDay as $date => $preloads) {
                $chartLabels[] = '"'.Factory::get('Cache\Tools')->formatTimestamp($date, 'D j. F').'"';
                $chartValues[] = $preloads;
            }

        }

        $totalPages = $wpdb->get_results('
            SELECT
                COUNT(*) as `total`
            FROM
                `'.$wpdb->prefix.'oso_super_cache_pages`
            WHERE
                `dont_cache`=0
                AND
                `is_404`=0
        ');

        $totalCachedPages = number_format_i18n($totalPages[0]->total);

        $averages = $wpdb->get_results('
            SELECT
                AVG(`runtime_without_cache`) as `average_runtime_without_cache`,
                AVG(`runtime_with_cache`) as `average_runtime_with_cache`
            FROM
                `'.$wpdb->prefix.'oso_super_cache_pages`
            WHERE
                `dont_cache`=0
                AND
                `is_404`=0
                AND
                `runtime_with_cache`>0
        ');

        $averageRuntimeWithoutCache = number_format_i18n($averages[0]->average_runtime_without_cache, 3);
        $averageRuntimeWithCache    = number_format_i18n($averages[0]->average_runtime_with_cache, 3);

        if (!empty($averages[0]->average_runtime_without_cache) && !empty($averages[0]->average_runtime_with_cache)) {
            $averagePerformanceIncreased = Factory::get('Cache\Tools')->floatRound($averages[0]->average_runtime_without_cache/$averages[0]->average_runtime_with_cache, 1);
        } else {
            $averagePerformanceIncreased = 0;
        }

        $preloadsForTodayResult = $wpdb->get_results('
            SELECT
                COUNT(*) as `total`
            FROM
                `'.$wpdb->prefix.'oso_super_cache_pages`
            WHERE
            (
                `next_update`="0000-00-00 00:00:00"
                OR
                `next_update`<DATE_FORMAT(NOW(), "%Y-%m-%d 23:59:59")
            )
                AND
                `dont_cache`=0
                AND
                `is_404`=0
        ');

        $scheduledPreloadsForToday = !empty($preloadsForTodayResult[0]->total) ? $preloadsForTodayResult[0]->total : 0 ;
        $maxPagePreloadsPerDay  =  '-' ;
        $lastPreloadStamp       = get_option('OSOSuperCachePreloadedLastRequest', '-');

        if (!empty($lastPreloadStamp['timestamp'])) {
            $timezone = get_option('gmt_offset');
            $lastPreloadStamp = Factory::get('Cache\Tools')->formatTimestamp($lastPreloadStamp['timestamp']+($timezone*3600));
        }

        include Factory::get('Cache\Backend\Backend')->templatePath.'/dashboard.html.php';
    }

    /**
     * saveSettings function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function saveSettings($data)
    {
        if (empty($data['cachePreset'])) {
            $data['cachePreset'] = 'default';
        }

        if ($data['cachePreset'] == 'custom') {
            $inactiveConfig = get_option('OSOSuperCacheConfigCustom', 'does not exist');

            if ($inactiveConfig === 'does not exist') {
                // Get active config
                $inactiveConfig = Factory::get('Cache\Config')->getConfig();
            }
        } elseif ($data['cachePreset'] == 'default') {
            // Default config
            $inactiveConfig = Factory::get('Cache\Config')->defaultConfig();
        } elseif ($data['cachePreset'] == 'compatibility-a') {
            // Compatibility A config
            $inactiveConfig = Factory::get('Cache\Config')->presetCompatibilityA();
        } elseif ($data['cachePreset'] == 'only-pages') {
            // Only pages config
            $inactiveConfig = Factory::get('Cache\Config')->presetOnlyPages();
        } elseif ($data['cachePreset'] == 'ecommerce') {
            // Ecommerce config
            $inactiveConfig = Factory::get('Cache\Config')->presetEcommerce();
        } elseif ($data['cachePreset'] == 'magazine') {
            // Magazine / Blog config
            $inactiveConfig = Factory::get('Cache\Config')->presetMagazine();
        } elseif ($data['cachePreset'] == 'corporate') {
            // Corporate config
            $inactiveConfig = Factory::get('Cache\Config')->presetCorporate();
        } elseif ($data['cachePreset'] == 'test-css') {
            // Test CSS config
            $inactiveConfig = Factory::get('Cache\Config')->presetTestCSS();
        } elseif ($data['cachePreset'] == 'test-js') {
            // Test JS config
            $inactiveConfig = Factory::get('Cache\Config')->presetTestJS();
        }

        // If current preset is custom preset, save active config
        if ($data['cachePreset'] != 'custom') {
            if (Factory::get('Cache\Config')->getCurrentPreset() == 'custom') {
                update_option('OSOSuperCacheConfigCustom', Factory::get('Cache\Config')->getConfig(), 'no');
            }
        }

        $inactiveConfig['cacheActivated'] = !empty($data['cacheActivated']) ? 'yes' : 'no';

        // Save config
        Factory::get('Cache\Config')->saveConfig($inactiveConfig, 'inactive');

        Factory::get('Cache\Config')->setCurrentPreset($data['cachePreset']);
    }

    /**
     * handleCacheMaintenance function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function handleCacheMaintenance($data)
    {
        global $wpdb;

        if (!empty($data['cacheMaintenance']) && !empty($data['cacheMaintenanceConfirmation'])) {

            if ($data['cacheMaintenance'] == 'refresh') {
                Factory::get('Cache\Frontend\Garbage')->clearCache();

                Factory::get('Cache\Backend\Backend')->addMessage($this->getCacheMaintenanceMessage('refresh'), 'success');
            }

            if ($data['cacheMaintenance'] == 'reset') {
                // First clear cache and remove css and js files
                Factory::get('Cache\Frontend\Garbage')->clearCache();

                // Then remove all pages from index
                $wpdb->query('
                    DELETE FROM
                        `'.$wpdb->prefix.'oso_super_cache_pages`
                ');

                Factory::get('Cache\Backend\Backend')->addMessage($this->getCacheMaintenanceMessage('reset'), 'success');
            }

            if ($data['cacheMaintenance'] == 'clearStylesPreCache') {
                Factory::get('Cache\Frontend\Garbage')->clearStylesPreCacheFiles();

                Factory::get('Cache\Backend\Backend')->addMessage($this->getCacheMaintenanceMessage('clearStylesPreCache'), 'success');
            }
        }
    }

//    /**
//     * handleCheckForUpdates function.
//     *
//     * @access public
//     * @return void
//     */
//    public function handleCheckForUpdates()
//    {
////        $availableVersion = Factory::get('Cache\API')->checkForUpdate();
////
////        if (!empty($availableVersion)) {
////            update_option('OSOSuperCacheUpdateAvailableVersion', $availableVersion, 'no');
////            update_option('OSOSuperCacheUpdateLastCheck', time(), 'no');
////        }
//    }

    /**
     * getCacheMaintenanceMessage function.
     *
     * @access public
     * @param mixed $maintenanceType
     * @return void
     */
    public function getCacheMaintenanceMessage($maintenanceType)
    {
        $message = '';

        if ($maintenanceType == 'refresh') {

            $message = _x('Cache refresh successfully.', 'Status message', 'oso-super-cache');

        } elseif ($maintenanceType == 'reset') {

            $message = _x('Cache reset successfully.', 'Status message', 'oso-super-cache');

        } elseif ($maintenanceType == 'clearStylesPreCache') {

            $message = _x('CSS pre-cache files cleared successfully.', 'Status message', 'oso-super-cache');

        }

        return $message;
    }
}
