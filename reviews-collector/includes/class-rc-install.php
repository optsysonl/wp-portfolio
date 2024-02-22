<?php
/**
 * Created by PhpStorm.
 * User: GreyGooroo
 * Date: 04.06.2019
 * Time: 18:36
 */

class RC_Install {
    public static function init(){
        add_action('init', array( __CLASS__, 'check_version'));
    }

    public static function check_version() {
        if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'reviewscollector_version' ), RC()->version, '<' ) ) {
            self::install();
            do_action( 'reviewscollector_updated' );
        }
    }

    public static function install (){
        if(!is_blog_installed()){
            return;
        }

        if('yes' === get_transient('rc_installing')){
            return;
        }

        set_transient('rc_installing', 'yes', MINUTE_IN_SECONDS * 10);

        //install
        self::create_tables();
        self::update_rc_version();
        self::create_options();

        delete_transient('rc_installing');
        do_action('reviewscollector_installed');
    }

    /**
     * Update RC version to current.
     */
    private static function update_rc_version() {
        delete_option( 'reviewscollector_version' );
        add_option( 'reviewscollector_version', RC()->version );
    }

    private static function create_options (){}

    /**
     * @description Set up the database tables which the plugin needs to function.
     *
     * Tables:
     *      rc_reviews - Table for storing user reviews.
     */
    private static function create_tables(){
        global $wpdb;

        $rc_db_version = get_option('rc_db_version');
        if($rc_db_version != RC_DB_VERSION) {
            $wpdb->hide_errors();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            dbDelta(self::get_schema());
            update_option( "rc_db_version", RC_DB_VERSION );
        }
    }


    /**
     * @description Get table schema
     * @return string
     */
    private static function get_schema() {
        global $wpdb;

        $collate = '';

        if ( $wpdb->has_cap( 'collation' ) ) {
            $collate = $wpdb->get_charset_collate();
        }

        $tables = "
            CREATE TABLE {$wpdb->prefix}rc_reviews (
              review_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
              review_author tinytext NOT NULL,
              review_author_email varchar(100) NULL,
              review_author_ip varchar(100) NULL,
              review_date datetime NOT NULL default '0000-00-00 00:00:00',
              review_content TEXT NOT NULL,
              review_rating tinyint(1) NOT NULL DEFAULT 0,
              review_approved tinyint(1) NOT NULL DEFAULT 0,
              review_type varchar(100) NULL,
              user_id BIGINT UNSIGNED NOT NULL,
              PRIMARY KEY (review_id)
            ) $collate;
        ";

        return $tables;
    }

}
RC_Install::init();
















