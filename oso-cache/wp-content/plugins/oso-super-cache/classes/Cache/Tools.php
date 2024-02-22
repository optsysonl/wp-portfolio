<?php
/*
 *
 */

namespace OSOSuperCache\Cache;

use OSOSuperCache\Factory;

class Tools
{

    private static $instance;

    private $generatedStrings = [];

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

    protected function __construct()
    {
    }

    /**
     * generateRandomString function.
     *
     * @access public
     * @param int $stringLength (default: 32)
     * @return void
     */
    public function generateRandomString($stringLength = 32)
    {
        $charPool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $generatedString = '';

        if ($stringLength <= 0) {
            $stringLength = 32;
        }

        for ($i=0; $i<$stringLength; $i++) {
            $index = 0;

            // PHP 7
            if (function_exists('random_int')) {
                $index = random_int(0, 61);
            } elseif (function_exists('mt_rand')) {
                $index = mt_rand(0, 61);
            } else {
                $index = rand(0, 61);
            }

            $generatedString .= $charPool[$index];
        }

        // Make sure, the generated string is unique
        if (isset($this->generatedStrings[$generatedString])) {
            $generatedString = $this->generateRandomString($stringLength);
        } else {
            $this->generateRandomString[$generatedString] = $generatedString;
        }

        return $generatedString;
    }

    /**
     * isArrayMultidimensional function.
     *
     * @access public
     * @param mixed $array
     * @return void
     */
    public function isArrayMultidimensional($array)
    {
        $isMultidimensional = false;

        if (!empty($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $isMultidimensional = true;
                    break;
                }
            }
        }

        return $isMultidimensional;
    }

    /**
     * isStringJSON function.
     *
     * @access public
     * @param mixed $string
     * @return void
     */
    public function isStringJSON($string)
    {
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE ? true : false;
    }

    /**
     * formatTimestamp function.
     *
     * @access public
     * @param mixed $timestamp
     * @param mixed $dateFormat (default: null)
     * @return void
     */
    public function formatTimestamp($timestamp, $dateFormat = null)
    {
        if (is_string($timestamp)) {
            $timestamp = strtotime($timestamp);
        }

        if (empty($dateFormat)) {
            $dateFormat = get_option('date_format');
            $timeFormat = get_option('time_format');

            $dateFormat = $dateFormat.' '.$timeFormat;
        }

        return date_i18n($dateFormat, $timestamp);
    }


    /**
     * independentRound function.
     * Because setLocale can make round() return a "float" with comma as decimal separator.
     *
     * @access public
     * @return void
     */
    public function floatRound($val, $precision = 0)
    {
        return str_replace(',', '.', round($val, $precision));
    }


    /**
     * findPositionInArray function.
     *
     * @access public
     * @param mixed $needle
     * @param mixed $haystack
     * @return void
     */
    public function findPositionInArray($needle, $haystack)
    {
        $pos    = 0;
        $found  = false;

        foreach ($haystack as $key => $value) {
            if ($value == $needle) {
                $found = true;
                break;
            }

            $pos++;
        }

        return $found ? $pos : false;
    }

    /**
     * addBeforeElement function.
     *
     * @access public
     * @param mixed $array
     * @param mixed $needle
     * @param mixed $list
     * @return void
     */
    public function addBeforeElement($array, $needle, $list)
    {
        // If element is not in our list
        if (empty($list[$needle])) {
            $list[key($array)] = current($array);
        } else {
            // Find needle in our list and add array before
            $pos = $this->findPositionInArray($needle, $list);
            $list = array_merge(
                array_slice($list, 0, $pos),
                [ key($array) => current($array) ],
                array_slice($list, $pos)
            );
        }

        return $list;
    }

    /**
     * isHttps function.
     *
     * @access public
     * @return void
     */
    public function isHttps()
    {
        $isHttps = false;

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $isHttps = true;
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            // If server is behind a load balancer
            $isHttps = true;
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
            // If server is behind a load balancer
            $isHttps = true;
        }

        return $isHttps;
    }

    /**
     * getHash function.
     *
     * @access public
     * @param mixed $toHash
     * @return void
     */
    public function getHash($toHash)
    {
        return sha1(serialize($toHash));
    }

    /**
     * getStringLength function.
     *
     * @access public
     * @param mixed $str
     * @return void
     */
    public function getStringLength ($str)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($str);
        } else {
            return strlen($str);
        }
    }

    /**
     * getDBTimestamp function.
     *
     * @access public
     * @return void
     */
    public function getDBTimestamp()
    {
        global $wpdb;

        $currentTimestamp = $wpdb->get_results('
            SELECT
                UNIX_TIMESTAMP() as "currentTimestamp"
        ');

        return $currentTimestamp[0]->currentTimestamp;
    }

    /**
     * getDBTime function.
     *
     * @access public
     * @return void
     */
    public function getDBTime()
    {
        global $wpdb;

        $currentTime = $wpdb->get_results('
            SELECT
                NOW() as "now"
        ');

        return $currentTime[0]->now;
    }
}
