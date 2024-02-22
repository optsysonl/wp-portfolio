<?php

namespace OSOSuperCache\Cache;

use OSOSuperCache\Factory;

class Update
{
    private static $instance;

    private $currentBlogId = '';

    public static function getInstance ()
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
    }

    public function handlePluginAPI($result, $action, $args)
    {
        if (!empty($action) && $action == 'plugin-information' && !empty($args->slug)) {
            if ($args->slug == dirname(OSO_SUPER_CACHE_SLUG)) {
                $result = Factory::get('Cache\API')->getPluginInformation();
            }
        }

        return $result;
    }

    public function handleTransientUpdatePlugins($transient)
    {
        if (isset($transient->response[OSO_SUPER_CACHE_SLUG])) {
            return $transient;
        }
        $updateInformation = Factory::get('Cache\API')->getLatestVersion();
        if (!empty($updateInformation)) {
            if (version_compare(OSO_SUPER_CACHE_VERSION, $updateInformation->new_version, '<')) {
                $transient->response[OSO_SUPER_CACHE_SLUG] = $updateInformation;
            }
        }
        return $transient;
    }
}



























