<?php
/*
Plugin Name: OSO Super Cache
Plugin URI:
Description: OSO Super Cache plugin
Author: OSO Web Studio
Author URI: https://www.oso-web.com
Version: 1.0.4
Text Domain: oso-super-cache
Domain Path: /languages
*/

define('OSO_SUPER_CACHE_VERSION', '1.0.4');
define('OSO_SUPER_CACHE_SLUG', plugin_basename(__FILE__));
define('OSO_SUPER_CACHE_URL', plugin_dir_url(__FILE__));

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('OSOSuperCache\Cache\Init')) {

    if (version_compare(phpversion(), '5.6', '>=')) {

        include_once plugin_dir_path(__FILE__).'classes/Autoloader.php';

        $Autoloader = new \OSOSuperCache\Autoloader();
	    $Autoloader->register();
    	$Autoloader->addNamespace('OSOSuperCache', realpath(plugin_dir_path(__FILE__).'/classes'));

        register_activation_hook(__FILE__, array(\OSOSuperCache\Factory::get('Cache\Init'), 'pluginActivated'));
        register_deactivation_hook(__FILE__, array(\OSOSuperCache\Factory::get('Cache\Init'), 'pluginDeactivated'));

        if (is_admin()) {
            \OSOSuperCache\Factory::get('Cache\Init')->initBackend();
        } else {
            \OSOSuperCache\Factory::get('Cache\Init')->initFrontend();
        }

        /* Update */
        \OSOSuperCache\Factory::get('Cache\Init')->initUpdateHooks();

        /* Call after upgrade process is complete */
//        add_action('upgrader_process_complete', array(\OSOSuperCache\Factory::get('Cache\Update'), 'upgradeComplete'), 10, 2);

        if (!function_exists('OSOSuperCacheHelper')) {
            function OSOSuperCacheHelper()
            {
                return \OSOSuperCache\Factory::get('Cache\ThirdPartyHelper');
            }
        }

    } else {
        add_action('admin_notices', function () {
        ?>
        <div class="notice notice-error">
            <p><?php _ex('Your PHP version is <a href="http://php.net/supported-versions.php" rel="noreferrer" target="_blank">outdated</a> and not supported by OSO Super Cache. Please disable OSO Super Cache, upgrade to PHP 5.6 or higher, and enable OSO Super Cache again. It is necessary to follow these steps in order.', 'Status message', 'oso-super-cache'); ?></p>
        </div>
        <?php
        });
    }
} else {
    add_action('admin_notices', function () {
        ?>
        <div class="notice notice-error">
            <p><?php
                if (defined('OSO_SUPER_CACHE_SLUG') && OSO_SUPER_CACHE_SLUG == 'oso-super-cache/oso-super-cache.php') {
                    _ex('<strong>OSO Super Cache</strong>', 'Status message', 'oso-super-cache');
                }
            ?></p>
        </div>
        <?php
    });
}