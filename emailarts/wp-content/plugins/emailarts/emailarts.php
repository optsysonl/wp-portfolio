<?php
/*
Plugin Name: EmailArts
Plugin URI:
Description: Newsletter SignUp plugin to connect for your website from EmailArts system
Version: 1.2.1
Author: OSO Web Studio
Author URI: https://www.oso-web.com
License:           GPL-3.0+
License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
Text Domain:       emailarts
*/

if ( ! defined( 'ABSPATH' ) ) {
    die( '-1' );
}

$dir = dirname(__FILE__);
define( 'WPEA_PLUGIN', __FILE__ );
define( 'WPEA_VERSION', '1.2.1');
define( 'WPEA_PLUGIN_DIR', $dir );
define( 'WPEA_PLUGIN_DIR_URI', plugin_dir_url( __FILE__ ));
define( 'WPEA_PLUGIN_FILE', __FILE__ );
define( 'WPEA_PLUGIN_SLUG', plugin_basename(__FILE__));
if ( ! defined( 'WPEA_LOAD_JS' ) ) {
    define( 'WPEA_LOAD_JS', true );
}

if ( ! defined( 'WPEA_LOAD_CSS' ) ) {
    define( 'WPEA_LOAD_CSS', true );
}

if ( ! defined( 'WPEA_VERIFY_NONCE' ) ) {
    define( 'WPEA_VERIFY_NONCE', false );
}
if ( ! defined( 'WPEA_VALIDATE_CONFIGURATION' ) ) {
    define( 'WPEA_VALIDATE_CONFIGURATION', true );
}

if ( ! defined( 'WPEA_USE_PIPE' ) ) {
    define( 'WPEA_USE_PIPE', true );
}

if ( ! defined( 'WPEA_AUTOP' ) ) {
    define( 'WPEA_AUTOP', true );
}

require_once WPEA_PLUGIN_DIR . '/load.php';
