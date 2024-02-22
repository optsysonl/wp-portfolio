<?php

defined( 'ABSPATH' ) || exit;

/**
 * Main Reviews Collector class
 *
 * @class ReviewsCollector
 */
final class ReviewsCollector {

    public $version = '1.0.3';
    public $db_version = '1.0.0';

    protected static $_instance = null;

    /**
     * Main ReviewsCollector instance.
     *
     * @return null|ReviewsCollector
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct(){
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    public function define_constants(){
        $upload_dir = wp_upload_dir( null, false );

        $this->define( 'RC_ABSPATH', dirname( RC_PLUGIN_FILE ) . '/' );
        $this->define( 'RC_PLUGIN_BASENAME', plugin_basename( RC_PLUGIN_FILE ) );
        $this->define( 'RC_PLUGIN_DIR_URI', plugin_dir_url( RC_PLUGIN_FILE ) );
//        $this->define( 'RC_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
        $this->define( 'RC_VERSION', $this->version );
        $this->define( 'RC_DB_VERSION', $this->db_version);
//        $this->define( 'REVIESCOLLECTOR_VERSION', $this->version );
        $this->define( 'RC_DELIMITER', '|' );
        $this->define( 'RC_LOG_DIR', $upload_dir['basedir'] . '/rc-logs/' );
        $this->define( 'RC_SESSION_CACHE_GROUP', 'RC_session_id' );
        $this->define( 'RC_TEMPLATE_DEBUG_MODE', false );
    }

    public function includes(){
        // abstracts
        include_once RC_ABSPATH . 'includes/abstracts/abstract-rc-object-query.php';

        // core classes
        include_once RC_ABSPATH . 'includes/rc-core-functions.php';
        include_once RC_ABSPATH . 'includes/class-rc-update.php';
        include_once RC_ABSPATH . 'includes/class-rc-install.php';
        include_once RC_ABSPATH . 'includes/class-rc-frontend.php';
        include_once RC_ABSPATH . 'includes/class-rc-ajax.php';

        include_once RC_ABSPATH . 'includes/class-rc-reviews-query.php'; //TODO remove
		include_once RC_ABSPATH . 'includes/class-rc-reviews-data.php';

		include_once RC_ABSPATH . 'includes/class-rc-post-shortcode.php';

		if(is_admin())
		{
			include_once RC_ABSPATH . 'includes/admin/class-rc-reviews-admin-data.php';
			include_once RC_ABSPATH . 'includes/admin/class-rc-admin.php';
		}
    }

    private function init_hooks(){
        register_activation_hook(RC_PLUGIN_FILE, array('RC_Install', 'install'));
        register_shutdown_function(array($this, 'log_errors'));

        add_action( 'init', array( $this, 'init' ), 0);
        add_action( 'init', array( 'RC_Frontend', 'init' ) );

        add_action('plugins_api', array('RC_Review_Update', 'handlePluginAPI'), 9002, 3);
        add_filter('pre_set_site_transient_update_plugins', array('RC_Review_Update', 'handleTransientUpdatePlugins'));
    }

    /**
     * @name is_request
     * @param {string} $type
     * @return boolean
     */
    private function is_request($type){
        switch($type){
            case 'admin':
                return is_admin();
        }
    }

    public function log_errors() {

    }

    public function init(){
        do_action('before_reviews_collector_init');

        $this->load_plugin_textdomain();

        do_action( 'reviews_collector_init' );
    }

    /**
     * @name load_plugin_textdomain
     * @description Load Localisation files.
     */
    public function load_plugin_textdomain() {
        $locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
        $locale = apply_filters( 'plugin_locale', $locale, 'reviews-collector' );

        unload_textdomain( 'reviews-collector' );
        load_textdomain( 'reviews-collector', WP_LANG_DIR . '/reviews-collector/reviews-collector-' . $locale . '.mo' );
        load_plugin_textdomain( 'reviews-collector', false, plugin_basename( dirname( RC_PLUGIN_FILE ) ) . '/i18n/languages' );
    }

    /**
     * Define constant if not already set.
     *
     * @param string      $name  Constant name.
     * @param string|bool $value Constant value.
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }
}