<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class Miscellaneous {

    private static $instance;

    public static function getInstance () {

        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __clone () {}

    private function __wakeup () {}

    protected function __construct () {}

    /**
     * handleSettings function.
     *
     * @access public
     * @return void
     */
    public function handleSettings () {

        // Disable emojis
        if (Factory::get('Cache\Config')->get('miscellaneousDisableEmojis')) {
            $this->disableEmojis();
        }

        // Disable generator
        if (Factory::get('Cache\Config')->get('miscellaneousDisableGenerator')) {
            $this->disableGenerator();
        }

        // Disable manifest
        if (Factory::get('Cache\Config')->get('miscellaneousDisableManifest')) {
            $this->disableManifest();
        }

        // Disable feeds
        if (Factory::get('Cache\Config')->get('miscellaneousDisableFeeds')) {
            $this->disableFeeds();
        }

        // Disable RSD
        if (Factory::get('Cache\Config')->get('miscellaneousDisableRSD')) {
            $this->disableRSD();
        }

        // Disable REST API
        if (Factory::get('Cache\Config')->get('miscellaneousDisableRESTAPI')) {
            $this->disableRESTAPI();
        }

        // Disable oEmbed
        if (Factory::get('Cache\Config')->get('miscellaneousDisableOEmbed')) {
            $this->disableOEmbed();
        }

        // Third party plugins

        // Slider Revolution Generator
        if (Factory::get('Cache\Config')->get('miscellaneousDisableTPPSliderRevolutionGenerator')) {
            $this->disableTTPSliderRevolutionGenerator();
        }

        // LayerSlider Generator
        if (Factory::get('Cache\Config')->get('miscellaneousDisableTPPLayerSliderGenerator')) {
            $this->disableTTPLayerSliderGenerator();
        }

        // Visual Composer Generator
        if (Factory::get('Cache\Config')->get('miscellaneousDisableTPPVisualComposerGenerator')) {
            $this->disableTTPVisualComposerGenerator();
        }


        add_action('wp_headers', array($this, 'set_cache_headers'));
    }

    /**
     * disableEmojis function.
     *
     * @access public
     * @return void
     */
    public function disableEmojis () {

        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        add_filter('emoji_svg_url', '__return_false');

    }

    public function set_cache_headers($headers){
        $checkboxBrowserCacheModifyHtaccess = Factory::get('Cache\Config')->get('browserCacheDisableXCacheHeaders', 'inactive') ? true : false;

        if($checkboxBrowserCacheModifyHtaccess){
            $headers["Content-Type"]= "text/html; charset=UTF-8";
            $headers['X-Cache-Enabled'] = 'False';
        }

        return $headers;
    }

    /**
     * disableGenerator function.
     *
     * @access public
     * @return void
     */
    public function disableGenerator () {
        remove_action('wp_head', 'wp_generator');
    }

    /**
     * disableManifest function.
     *
     * @access public
     * @return void
     */
    public function disableManifest () {
        remove_action('wp_head', 'wlwmanifest_link');
    }

    /**
     * disableFeeds function.
     *
     * @access public
     * @return void
     */
    public function disableFeeds () {
        remove_action('wp_head', 'feed_links', 2);
        remove_action('wp_head', 'feed_links_extra', 3);
    }

    /**
     * disableRSD function.
     *
     * @access public
     * @return void
     */
    public function disableRSD () {
        remove_action('wp_head', 'rsd_link');
    }

    /**
     * disableRESTAPI function.
     *
     * @access public
     * @return void
     */
    public function disableRESTAPI () {
        remove_action('wp_head', 'rest_output_link_wp_head');
    }

    /**
     * disableOEmbed function.
     *
     * @access public
     * @return void
     */
    public function disableOEmbed () {
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
    }

    /**
     * disableTTPSliderRevolutionGenerator function.
     *
     * @access public
     * @return void
     */
    public function disableTTPSliderRevolutionGenerator () {
        add_filter('revslider_meta_generator', '__return_false');
    }

    /**
     * disableTTPLayerSliderGenerator function.
     *
     * @access public
     * @return void
     */
    public function disableTTPLayerSliderGenerator () {
        add_filter('ls_meta_generator', [Factory::get('Cache\Frontend\Miscellaneous'), 'returnEmptyString']);
    }

    /**
     * disableTTPVisualComposerGenerator function.
     *
     * @access public
     * @return void
     */
    public function disableTTPVisualComposerGenerator () {

        if (function_exists('visual_composer')) {
            remove_action('wp_head', [visual_composer(), 'addMetaData']);
        }
    }

    /**
     * returnEmptyString function.
     *
     * @access public
     * @return void
     */
    public function returnEmptyString () {
        return '';
    }
}