<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class InstantPreloader
{

    private static $instance;

    private $config;

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

    public function __construct()
    {
    }

    /**
     * preload function.
     *
     * @access public
     * @param mixed $url
     * @param mixed $prefix
     * @return void
     */
    public function preload($url, $prefix = null)
    {
        $returnValue = false;

        // Check if requested URL is in cache index and reset it
        Factory::get('Cache\Frontend\Garbage')->refreshCacheOfURL($url, $prefix);

        // Set Cache Custom header if prefix is set
        $headers = [];

        if (!empty($prefix)) {
            $headers[] = 'X-OSO-Super-Cache-Custom: '.$prefix;
        }

        $args = [
            'timeout'=>45,
            'user_agent'=>'OSO-Super-Cache-Instant-Preloader/1.0',
            'headers'=>$headers,
        ];

        $response = wp_remote_get($url, $args);

        if (!empty($response) && is_array($response) && $response['response']['code'] == 200) {
            $returnValue = true;
        }

        unset($response);

        return $returnValue;
    }
}