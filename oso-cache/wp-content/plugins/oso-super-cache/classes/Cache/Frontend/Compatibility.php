<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class Compatibility {

    private static $instance;

    public static function getInstance () {

        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct () {}

    /**
     * shouldCachingBeSkipped function.
     *
     * @access public
     * @return void
     */
    public function shouldCachingBeSkipped ()
    {
        $skipCaching = false;

        // AMP check when fragment caching is active
        if (Factory::get('Cache\Config')->get('fragmentCaching')) {
            // Check if url is an AMP url
            if (preg_match('/^(.*)\/amp(\/)?$/', Factory::get('Cache\Frontend\Resolver')->getURLInfo('path'))) {
                $skipCaching = true;
            }
        }

        return $skipCaching;
    }
}