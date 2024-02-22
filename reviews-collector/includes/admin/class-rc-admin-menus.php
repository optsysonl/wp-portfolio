<?php

defined('ABSPATH') || exit;

if (class_exists('RC_Admin_Menus', false)) {
    return new RC_Admin_Menus();
}

class RC_Admin_Menus
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'admin_menu'), 9);
        add_action('admin_menu', array($this, 'admin_add_review'), 20);
        add_action('admin_menu', array($this, 'settings_menu'), 20);
    }

    public function admin_menu()
    {
        global $menu;

        $menu[] = array('', 'read', 'separator', '', 'wp-menu-separator menu-icon-dashboard');
        add_menu_page(
            __('Reviews Collector', 'review-collector'),
            __('Reviews Collector', 'review-collector'),
            'manage_options',
            'review-collector',
            array($this, 'reviews_page'),
            null,
            '55.1'
        );
    }

    public function admin_add_review()
    {
        add_submenu_page(
            'review-collector',
            __('Add New Review', 'review-collector'),
            __('Add New Review', 'review-collector'),
            'manage_options',
            'rc_reviews_form',
            array($this, 'add_review')
        );
    }

    public function settings_menu()
    {
        add_submenu_page(
            'review-collector',
            __('Settings', 'review-collector'),
            __('Settings', 'review-collector'),
            'manage_options',
            'rc-settings',
            array($this, 'settings_page_init')
        );
    }

    public function settings_page_init()
    {
        $tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';

        if (!did_action('wp_enqueue_media')) {
            wp_enqueue_media();
        }
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('rc_admin_custom_js', RC_PLUGIN_DIR_URI . 'assets/js/admin-script.js', array('jquery', 'wp-color-picker'), '', true);
        ?>

        <div class="wrapper">
            <h1>Reviews collector</h1>

            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2 cmb2-options-page">

                    <div id="postbox-container-1" class="postbox-container">
                        sidebar
                    </div>
                    <div id="postbox-container-2" class="postbox-container">
                        <div id="rc-main-settings">
                            <div class="rc-tabs-wrapper cmb2-options-page">
                                <div class="rc-tabs-pannel nav-tab-wrapper">
                                    <a
                                            class="nav-tab <?php if ($tab == 'settings') {
                                                echo 'nav-tab-active';
                                            } ?>"
                                            href="<?php echo esc_url(add_query_arg('tab', 'settings', 'admin.php?page=rc-settings')); ?>"
                                    >
                                        <?php esc_html_e('Main Settings', 'reviewcollector'); ?>
                                    </a>
                                    <a
                                            class="nav-tab <?php if ($tab == 'popup-setting') {
                                                echo 'nav-tab-active';
                                            } ?>"
                                            href="<?php echo esc_url(add_query_arg('tab', 'popup-setting', 'admin.php?page=rc-settings')); ?>"
                                    >
                                        <?php esc_html_e('Form settings', 'reviewcollector'); ?>
                                    </a>
                                    <a
                                            class="nav-tab <?php if ($tab == 'sharing-setting') {
                                                echo 'nav-tab-active';
                                            } ?>"
                                            href="<?php echo esc_url(add_query_arg('tab', 'sharing-setting', 'admin.php?page=rc-settings')); ?>"
                                    >
                                        <?php esc_html_e('Share Settings', 'reviewcollector'); ?>
                                    </a>
                                    <a
                                            class="nav-tab <?php if ($tab == 'reviews-setting') {
                                                echo 'nav-tab-active';
                                            } ?>"
                                            href="<?php echo esc_url(add_query_arg('tab', 'reviews-setting', 'admin.php?page=rc-settings')); ?>"
                                    >
                                        <?php esc_html_e('Reviews settings', 'reviewcollector'); ?>
                                    </a>
                                </div>

                                <div class="reviews-collector-page">
                                    <?php
                                    switch ($tab) {
                                        case 'settings' :
                                            require_once RC_ABSPATH . 'includes/admin/templates/html-admin-settings.php';
                                            break;
                                        case 'popup-setting' :
                                            require_once RC_ABSPATH . 'includes/admin/templates/html-admin-popup-settings.php';
                                            break;
                                        case 'sharing-setting' :
                                            require_once RC_ABSPATH . 'includes/admin/templates/html-admin-sharing-settings.php';
                                            break;
                                        case 'reviews-setting' :
                                            require_once RC_ABSPATH . 'includes/admin/templates/html-admin-reviews-settings.php';
                                            break;
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>

        <?php
    }

    public function add_review()
    {
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Add New Review</h1>
            <hr class="wp-header-end">
            <h2 class="screen-reader-text">Add New Review</h2>
            <?php
            require_once RC_ABSPATH . 'includes/admin/templates/html-admin-review-form.php';
            ?>
            <div id="ajax-response"></div>
            <br class="clear">
        </div>
        <?php
    }

    public function reviews_page()
    {
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Reviews List</h1>
            <hr class="wp-header-end">
            <h2 class="screen-reader-text">Reviews Items list</h2>
            <form id="reviews-form" method="post">
                <?php echo rc_get_admin_reviews(); ?>
            </form>
            <div id="ajax-response"></div>
            <br class="clear">
        </div>
        <?php
    }

    public function shortcodes_list_init()
    {
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Shortcode list</h1>
            <hr class="wp-header-end">
            <?php echo rc_get_admin_shortcodes(); ?>
            <div id="ajax-response"></div>
            <br class="clear">
        </div>
        <?php
    }

}

return new RC_Admin_Menus();















