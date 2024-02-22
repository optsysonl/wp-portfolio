<?php
/*
 *
 */

namespace OSOSuperCache\Cache;

use OSOSuperCache\Factory;

class Init
{

    private static $instance;

    public $runtimeStart;
    public $runtimeEnd;

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

    /* Init plugin */
    protected function __construct()
    {
    }

    public function initBackend()
    {
        // Init all actions and filters which are relevant for the backend
        Factory::get('Cache\Backend\Backend');
    }

    public function initFrontend()
    {
        $this->runtimeStart = microtime(1);

        // Init all actions and filters which are relevant for the frontend
        Factory::get('Cache\Frontend\Frontend');
    }

    public function initUpdateHooks()
    {
        /* Overwrite API URL when request infos about OSOSuperCache */
        add_action('plugins_api', [Factory::get('Cache\Update'), 'handlePluginAPI'], 9002, 3);

        /* Register Hook for checking for updates */
        add_filter('pre_set_site_transient_update_plugins', [Factory::get('Cache\Update'), 'handleTransientUpdatePlugins']);
    }

    public function pluginActivated()
    {
        Factory::get('Cache\Install')->installPlugin();

        // If cache system was active before, check if htaccess modification was true and add settings again
        if (Factory::get('Cache\Config')->get('browserCacheModifyHtaccess')) {
            Factory::get('Cache\Backend\AdvancedSettings')->modifyHtaccess(1);
        }

        if (Factory::get('Cache\Config')->get('browserSecurityContentSecurityPolicyHeader') && Factory::get('Cache\Config')->get('browserSecurityContentSecurityPolicy')) {
            Factory::get('Cache\Backend\AdvancedSettings')->modifyHtaccessSecurity(1);
        }

        // Remove activated message to display message again
        delete_option('OSOSuperCacheActivatedMessage');
        delete_option('OSOSuperCacheSystemChangedMessage');
    }

    public function pluginDeactivated()
    {
        // Remove htaccess settings
        Factory::get('Cache\Backend\AdvancedSettings')->modifyHtaccess(0);
        Factory::get('Cache\Backend\AdvancedSettings')->modifyHtaccessSecurity(0);
    }

    public function getTotalRuntime()
    {
        return Factory::get('Cache\Tools')->floatRound(microtime(1) - $this->runtimeStart, 8);
    }
}
