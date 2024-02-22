<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class CDN {

    private static $instance;

    private $cdnProvider;

    public static function getInstance () {

        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __clone () {}

    private function __wakeup () {}

    protected function __construct () {
        $this->cdnProvider = Factory::get('Cache\Config')->get('cdnProvider');
    }

    /**
     * processHTML function.
     *
     * @access public
     * @param mixed &$sourceCode
     * @return void
     */
    public function processHTML (&$sourceCode) {
        Factory::get('Cache\Frontend\\'.$this->cdnProvider)->processHTML($sourceCode);
    }

    /**
     * modifySrc function. Modifies src attribute of various media-tags
     *
     * @access public
     * @param mixed &$sourceCode
     * @param mixed $callback
     * @return void
     */
    public function modifySrc (&$sourceCode, $callback) {
        $sourceCode = preg_replace_callback('/\<(audio|embed|img|input|script|source|track|video)([^>]+?)(src=["|\']?([^"\']+)["|\']?)([^>]*?)\>/', $callback, $sourceCode);
    }

    /**
     * modifySrcset function. Modifies srcset attribute in img-tags
     *
     * @access public
     * @param mixed &$sourceCode
     * @param mixed $callback
     * @return void
     */
    public function modifySrcset (&$sourceCode, $callback) {
        $sourceCode = preg_replace_callback('/\<(img|source)([^>]+?)(srcset=["|\']?([^"\']+)["|\']?)([^>]*?)\>/', $callback, $sourceCode);
    }

    /**
     * modifyLink function. Modifies link-tags if they link to a stylesheet
     *
     * @access public
     * @param mixed &$sourceCode
     * @param mixed $callback
     * @return void
     */
    public function modifyLink (&$sourceCode, $callback) {
        // no rel OR rel is stylesheet or icon -> replace href
        $sourceCode = preg_replace_callback('/\<link([^>]+?) (href=["|\']?([^"\']+)["|\']?) ([^>]*?)\>/', $callback, $sourceCode);
    }

    /**
     * @method modifyBackgroundUrl
     * modified background url to image
     *
     * @param mixed $sourceCode
     * @param mixed $callback
     * $return void
     */
    public function modifyBackgroundUrl(&$sourceCode, $callback){
        $sourceCode = preg_replace_callback('/\url\((["|\']?([^>]+?)["|\']?)\);/', $callback, $sourceCode);
    }
}


















