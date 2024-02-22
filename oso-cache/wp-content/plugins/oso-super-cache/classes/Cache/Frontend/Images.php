<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class Images
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
     * registerScript function.
     *
     * @access public
     * @return void
     */
    public function registerScript()
    {
        if (Factory::get('Cache\Config')->get('imagesLazyLoad')) {
            wp_enqueue_script('oso-super-cache-lazy-load', OSO_SUPER_CACHE_URL.'vendor/jquery-lazyload/lazyload.min.js', ['jquery'], null, true);
        }
    }

    /**
     * optimize function.
     *
     * @access public
     * @param mixed &$html
     * @return void
     */
    public function optimize(&$html)
    {
        // If JS merging is disabled we have to mask all script tags
        if (Factory::get('Cache\Config')->get('scriptsMerge') == false) {
            $html = preg_replace_callback('/<script.*<\/script>/Us', [Factory::get('Cache\Frontend\Optimizer'), 'maskTags'], $html);
        }

        $html = preg_replace_callback('/<img.*>/Us', [$this, 'modifyImageTag'], $html);

        // Parse script tags back
        if (Factory::get('Cache\Config')->get('scriptsMerge') == false) {
            Factory::get('Cache\Frontend\Optimizer')->reInsertPreservedTags($html, true);
        }
    }

    /**
     * modifyImageTag function.
     *
     * @access public
     * @param mixed $tag
     * @return void
     */
    public function modifyImageTag($tag)
    {
        $excludeFromLazyLoading = false;
        $excludedCSSClasses = Factory::get('Cache\Config')->get('imagesLazyLoadExclude');

        $imageClass = [];
        $imageCSSClasses = [];

        preg_match('/class=("|\'){1}([^"\']+)("|\'){1}/', $tag[0], $imageClass);

        if (!empty($imageClass[2])) {

            $imageCSSClasses = explode(' ', $imageClass[2]);

            // Check if class is excluded
            if (!empty($imageCSSClasses) && !empty($excludedCSSClasses)) {
                foreach ($imageCSSClasses as $cssClassName) {
                    if (in_array($cssClassName, $excludedCSSClasses)) {
                        $excludeFromLazyLoading = true;

                        break;
                    }
                }
            }

            if ($excludeFromLazyLoading === false) {
                // Modify class attribute
                $tag[0] = preg_replace('/class=("|\'){1}([^"\']+)("|\'){1}/', 'class=$1$2 lazyload$3', $tag[0]);
            }

        } else {
            // Add class attribute
            $tag[0] = str_replace('<img', '<img class="lazyload"', $tag[0]);
        }

        if ($excludeFromLazyLoading === false) {
            // Replace image src
            $tag[0] = preg_replace('/src=("|\'){1}([^"\']+)("|\'){1}/', 'src=$1data:image/gif;base64,R0lGODdhAQABAIAAANk7awAAACH5BAEAAAEALAAAAAABAAEAAAICTAEAOw==$3 data-src=$1$2$3', $tag[0]);

            // Modify srcset
            $tag[0] = preg_replace('/srcset=("|\'){1}([^"\']+)("|\'){1}/', 'data-srcset=$1$2$3', $tag[0]);
        }

        return $tag[0];
    }
}
