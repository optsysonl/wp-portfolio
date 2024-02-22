<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class Header
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

    public function setConfig($configVars)
    {
        $this->config = $configVars;
    }

    public function getHeader($pathToFile)
    {
        $lastModified = filemtime($pathToFile);

        // ETag
        $clientETag             = !empty($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : null;
        $clientLastModified     = !empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? trim($_SERVER['HTTP_IF_MODIFIED_SINCE']) : null;
        $clientAcceptedEncoding = !empty($_SERVER['HTTP_ACCEPT_ENCODING']) ? trim($_SERVER['HTTP_ACCEPT_ENCODING']) : null;

        $serverLastModified = gmdate('D, d M Y H:i:s', $lastModified).' GMT';
        $serverETagRaw      = sha1($lastModified.$clientAcceptedEncoding.$pathToFile);
        $serverETag         = '"'.$serverETagRaw.'"';

        // Cache-Control Header
        if (!empty($this->config['browserCacheSetControlHeader'])) {
            $this->getCacheControlHeader($this->config['browserCacheControlPolicy'], $this->config['browserCacheControlHeaderExpiresLifetime']);
        }

        // Expires Header
        if (!empty($this->config['browserCacheControlHeaderExpiresLifetime'])) {
            header('Expires: '.gmdate('D, d M Y H:i:s', $lastModified+$this->config['browserCacheControlHeaderExpiresLifetime']).' GMT', true);
        }

        // Last Modified Header
        if (!empty($this->config['browserCacheSetLastModified'])) {
            header('Last-Modified: '.$serverLastModified, true);
        }

        // ETag Header
        if (!empty($this->config['browserCacheSetETag'])) {
            header('ETag: '.$serverETag, true);
        }

        if (empty($_SERVER['HTTP_PRAGMA']) || $_SERVER['HTTP_PRAGMA'] != 'no-cache') {
            if ($clientLastModified == $serverLastModified && strpos($clientETag, $serverETagRaw) !== false) {
                header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
                exit;
            }
        }
    }

    public function getDNSPrefetchHeader()
    {
        header('X-DNS-Prefetch-Control: on', true);
    }

    /**
     * getContentTypeHeaderCode function.
     *
     * @access public
     * @param mixed $mimeType
     * @param bool $returnCode (default: false)
     * @return void
     */
    public function getContentTypeHeaderCode($mimeType, $returnCode = false)
    {
        if ($returnCode) {
            return "header(\"Content-Type: ".$mimeType."; charset=".get_option('blog_charset')."\", true);";
        } else {
            header('Content-Type: '.$mimeType.'; charset='.get_option('blog_charset'), true);
        }
    }

    public function classLocation()
    {
        return __FILE__;
    }

    public function getCacheControlHeader($cacheControlPolicy, $maxAge)
    {
        /* Great article https://devcenter.heroku.com/articles/increasing-application-performance-with-http-cache-headers - helped a lot */

        if ($cacheControlPolicy == 'public') {
            header('Cache-Control:public', true);
        } elseif ($cacheControlPolicy == 'private') {
            header('Cache-Control:private', true);
        } elseif ($cacheControlPolicy == 'public-max-age') {
            header('Cache-Control:public, max-age='.$maxAge, true);
        } elseif ($cacheControlPolicy == 'private-max-age') {
            header('Cache-Control:private, max-age='.$maxAge, true);
        } elseif ($cacheControlPolicy == 'no-cache') {
            header('Cache-Control:no-cache, no-store, max-age=0', true);
        }
    }

    public function getHeaderRelatedConfigs()
    {
        return [
            'browserCacheSetControlHeader'              =>Factory::get('Cache\Config')->get('browserCacheSetControlHeader'),
            'browserCacheControlPolicy'                 =>Factory::get('Cache\Config')->get('browserCacheControlPolicy'),
            'browserCacheSetLastModified'               =>Factory::get('Cache\Config')->get('browserCacheSetLastModified'),
            'browserCacheControlHeaderExpiresLifetime'  =>Factory::get('Cache\Config')->get('browserCacheControlHeaderExpiresLifetime'),
            'browserCacheSetETag'                       =>Factory::get('Cache\Config')->get('browserCacheSetETag'),
            'browserCacheSetOSOSuperCacheTag'            =>Factory::get('Cache\Config')->get('browserCacheSetOSOSuperCacheTag'),
        ];
    }
}
