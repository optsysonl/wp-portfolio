<?php
/*
 *
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

if (version_compare(phpversion(), '5.6', '>=')) {

    include_once plugin_dir_path(__FILE__).'classes/Autoloader.php';

    $Autoloader = new \OSOSuperCache\Autoloader();
	$Autoloader->register();
	$Autoloader->addNamespace('OSOSuperCache', realpath(plugin_dir_path(__FILE__).'/classes'));

    \OSOSuperCache\Factory::get('Cache\Uninstall')->uninstallPlugin();
}