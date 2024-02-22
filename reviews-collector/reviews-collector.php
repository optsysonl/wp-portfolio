<?php
/*
 * Plugin Name: Reviews Collector
 * Plugin URI:
 * Description: OSO Reviews collector plugin.
 * Version: 1.0.3
 * Author: OSO Web Studio
 * Author URI: https://www.oso-web.com
 * License:
 * Text Domain: reviews-collector
 */

defined( 'ABSPATH' ) || exit;

define('RC_PLUGIN_FILE', __FILE__ );
define('RC_PLUGIN_SLUG', plugin_basename(__FILE__));

if(!class_exists('ReviewsCollector')){
    include_once dirname( __FILE__ ) . '/includes/class-reviewscollector.php';
}

function RC() {
    return ReviewsCollector::instance();
}

$GLOBALS['ReviewsCollector'] = RC();