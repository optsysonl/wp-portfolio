<?php
/*
 *
 */

namespace OSOSuperCache\Cache;

use OSOSuperCache\Factory;

class Log
{

    private static $instance;

    private $logs           = [];
    private $uniqueToken    = [];

    public static function getInstance()
    {

        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->uniqueToken = Factory::get('Cache\Tools')->generateRandomString(14);
    }

    /**
     * addLog function.
     *
     * @access public
     * @param mixed $msg
     * @param string $level (default: '')
     * @return void
     */
    public function addLog($source, $msg, $level = 'info')
    {

        if (Factory::get('Cache\Debug')->isDebugEnabled()) {
            if (!in_array($level, ['error', 'warning', 'info'])) {
                $level = 'info';
            }

            $time = date('Y-m-d H:i:s');

            $this->logs[] = [
                'timestamp'=>$time,
                'source'=>$source,
                'level'=>$level,
                'message'=>$msg,
            ];

            if (Factory::get('Cache\Debug')->isDebugLogWritable()) {
                file_put_contents(Factory::get('Cache\Debug')->getDebugLogFile(), $this->uniqueToken."\t".$time."\t".$level."\t".$source."\t".$msg."\n", FILE_APPEND);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * getLogs function.
     *
     * @access public
     * @return void
     */
    public function getLogs()
    {
        return $this->logs;
    }
}
