<?php
/*
 *
 */

namespace OSOSuperCache\Cache;

use OSOSuperCache\Factory;

class Debug
{
    private static $instance;

    private $debugEnabled = false;

    public static function getInstance ()
    {

        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct()
    {
        if (defined('OSO_SUPER_CACHE_DEBUG') && OSO_SUPER_CACHE_DEBUG == true) {
            $this->debugEnabled = true;
        }
    }

    /**
     * isDebugEnabled function.
     *
     * @access public
     * @return void
     */
    public function isDebugEnabled()
    {
        return $this->debugEnabled;
    }

    /**
     * isDebugLogWritable function.
     *
     * @access public
     * @return void
     */
    public function isDebugLogWritable()
    {
        if (defined('OSO_SUPER_CACHE_DEBUG_WRITE_TO_FILE')) {
            if (!file_exists(OSO_SUPER_CACHE_DEBUG_WRITE_TO_FILE)) {
                touch(OSO_SUPER_CACHE_DEBUG_WRITE_TO_FILE);
            }

            if (is_writable(OSO_SUPER_CACHE_DEBUG_WRITE_TO_FILE)) {
                return true;
            }
        }

        return false;
    }

    /**
     * getDebugLogFile function.
     *
     * @access public
     * @return void
     */
    public function getDebugLogFile()
    {
        if (defined('OSO_SUPER_CACHE_DEBUG_WRITE_TO_FILE')) {
            return OSO_SUPER_CACHE_DEBUG_WRITE_TO_FILE;
        }

        return false;
    }
}
