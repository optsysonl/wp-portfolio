<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class Minify
{
    private static $instance;

    private $preservedTags = [];

    public static function getInstance ()
    {

        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct()
    {
        include_once realpath(__DIR__.'/../../../vendor/minify/src/Minify.php');
        include_once realpath(__DIR__.'/../../../vendor/minify/src/JS.php');
        include_once realpath(__DIR__.'/../../../vendor/minify/src/CSS.php');
        include_once realpath(__DIR__.'/../../../vendor/minify/src/Exception.php');
        include_once realpath(__DIR__.'/../../../vendor/minify/src/Exceptions/BasicException.php');
        include_once realpath(__DIR__.'/../../../vendor/minify/src/Exceptions/FileImportException.php');
        include_once realpath(__DIR__.'/../../../vendor/minify/src/Exceptions/IOException.php');
        include_once realpath(__DIR__.'/../../../vendor/path-converter/src/ConverterInterface.php');
        include_once realpath(__DIR__.'/../../../vendor/path-converter/src/Converter.php');
        include_once realpath(__DIR__.'/../../../vendor/path-converter/src/NoConverter.php');

        include_once realpath(__DIR__.'/../../../vendor/YUI-CSS-compressor-PHP-port/src/Colors.php');
        include_once realpath(__DIR__.'/../../../vendor/YUI-CSS-compressor-PHP-port/src/Command.php');
        include_once realpath(__DIR__.'/../../../vendor/YUI-CSS-compressor-PHP-port/src/Minifier.php');
        include_once realpath(__DIR__.'/../../../vendor/YUI-CSS-compressor-PHP-port/src/Utils.php');
    }

    /**
     * minifyCSS function.
     *
     * @access public
     * @param mixed &$css
     * @return void
     */
    public function minifyCSS(&$css)
    {
        $Compressor = new \tubalmartin\CssMin\Minifier();
        $Compressor->removeImportantComments();
        $css = $Compressor->run($css);
    }

    /**
     * minifyJS function.
     *
     * @access public
     * @param mixed &$js
     * @return void
     */
    public function minifyJS(&$js)
    {
        $Minifier = new \MatthiasMullie\Minify\JS();
        $Minifier->add($js);
        $js = $Minifier->minify();
    }

    /**
     * cleanHTML function.
     *
     * @access public
     * @param mixed &$html
     * @return void
     */
    public function cleanHTML(&$html)
    {
        // Check if we should remove whitespace
        if (Factory::get('Cache\Config')->get('minifyRemoveWhitespace')) {

            // Save some tags before removing html comments or whitespace
            $this->preserveTags($html);
        }

        // Remove HTML comments
        if (Factory::get('Cache\Config')->get('minifyRemoveHTMLComments')) {

            // Escape Google Snippets
            $html = preg_replace_callback('/(<!--google(on|off)\:([^\>]+)--\>)/Us', [$this, 'maskTags'], $html);

            // Fix wrong conditional comment
            $html = str_replace(']><!-->', ']>-->', $html);

            $search = ['/<!--(?!\[|<!\[).*?-->/s'];

            $replace = [''];

            $html = preg_replace($search, $replace, $html);
        }

        if (Factory::get('Cache\Config')->get('minifyRemoveWhitespace')) {

            // Remove whitespace
            $html = preg_replace('/\s+/', ' ', $html);

            $html = trim($html);
        }

        if (!empty($this->preservedTags)) {

            foreach ($this->preservedTags as $uniqueToken => $tag) {
                $html = str_replace($uniqueToken, $tag, $html);
            }

            // Free resources
            unset($this->preservedTags);
        }
    }

    /**
     * preserveTags function.
     *
     * @access public
     * @param mixed &$html
     * @return void
     */
    public function preserveTags(&$html)
    {
        // preserve fragment caching code
        $html = preg_replace_callback('/\<\!--\[oso-super-cache start: '.Factory::get('Cache\Config')->get('fragmentCachingMaskPhrase').'\]\-\-\>(.*)\<\!--\[oso-super-cache end: '.Factory::get('Cache\Config')->get('fragmentCachingMaskPhrase').'\]--\>/Us', [$this, 'maskTags'], $html);

        $html = preg_replace_callback('/<((pre|script|style)[^>]*)>(.*?)\<\/\2>/s', [$this, 'maskTags'], $html);
    }

    /**
     * maskTags function.
     *
     * @access public
     * @param mixed $tag
     * @return void
     */
    public function maskTags($tag)
    {
        $uniqueToken = Factory::get('Cache\Tools')->generateRandomString(32);

        $this->preservedTags[$uniqueToken] = $tag[0];

        return $uniqueToken;
    }
}
