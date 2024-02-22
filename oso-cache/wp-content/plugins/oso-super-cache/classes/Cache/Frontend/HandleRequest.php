<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class HandleRequest
{

    private static $instance;

    public $cacheRequest = false;

    // If a user is logged in and fragmented caching is active
    // we need to compile the code and have to activate the caching buffer
    public $cacheRequestButDontSave = false;

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
     * processPageRequest function.
     *
     * @access public
     * @return void
     */
    public function processPageRequest()
    {
        // Separate cache file for mobile devices
        if (Factory::get('Cache\Config')->get('cacheSeparateFileByDeviceType')) {

            include_once realpath(__DIR__.'/../../../vendor/Mobile-Detect/Mobile_Detect.php');

            $MobileDetect = new \Mobile_Detect;

            if (Factory::get('Cache\Config')->get('cacheSeparateFileByDeviceType') === 'mobile') {
                if ($MobileDetect->isMobile() && !$MobileDetect->isTablet()) {
                    Factory::get('Cache\Frontend\Resolver')->setCustomPrefix('mobile_');
                }
            }

            if (Factory::get('Cache\Config')->get('cacheSeparateFileByDeviceType') === 'mobile+tablet') {
                if ($MobileDetect->isMobile() && !$MobileDetect->isTablet()) {
                    Factory::get('Cache\Frontend\Resolver')->setCustomPrefix('mobile_');
                } elseif ($MobileDetect->isTablet()) {
                    Factory::get('Cache\Frontend\Resolver')->setCustomPrefix('tablet_');
                }
            }
        }

        // This hook allows you to call e.g. Resolver->setCustomPrefix to set a prefix which is useful when your design uses a non-responsive mobile theme
        do_action('oso_super_cache_process_request');

        // Don't handle requests with $_POST
        if (empty($_POST)) {
            // Check if page should be cached
            if (Factory::get('Cache\Frontend\Exceptions')->shouldPageBeCached()) {
                // Check if page is in cache
                if (Factory::get('Cache\Frontend\Cache')->isRequestedPageCached() && !Factory::get('Cache\Frontend\Exceptions')->isCacheLifetimeOver()) {
                    // yes, load cache (loadCache also executes the exit command)
                    Factory::get('Cache\Frontend\Cache')->loadCacheFile();
                } else {
                    // Never cache pages from logged in users
                    if (!Factory::get('Cache\Frontend\Exceptions')->isUserLoggedIn()) {
                        // This value will maybe changed to false at a later point
                        $this->cacheRequest = true;
                    }

                    // You can use this hook to overwrite cacheRequest or add additional rules
                    do_action('oso_super_cache_process_request_done');
                }
            }
        }
    }

    /**
     * startCaching function.
     *
     * @access public
     * @return void
     */
    public function startCaching()
    {
        $startBuffer = false;

        if ($this->cacheRequest == true) {
            // Get all available data about requested page.

            // Last chance to stop buffer to start.
            Factory::get('Cache\Frontend\Resolver')->collectPageData();

            // Don't cache 404 pages
            if (is_404() && !Factory::get('Cache\Config')->get('cache404Pages')) {
                $this->cacheRequest = false;
            }

            // Don't cache feeds
            if (is_feed() && !Factory::get('Cache\Config')->get('cacheFeeds')) {
                $this->cacheRequest = false;
            }

            // Don't cache page if an unknown query var was in requested URL
            if (Factory::get('Cache\Frontend\Resolver')->isUnknownQueryVarPresent()) {
                $this->cacheRequest = false;
            }

            // Don't cache specific post types
            if (!Factory::get('Cache\Frontend\Exceptions')->shouldPostTypeBeCached()) {
                $this->cacheRequest = false;
            }

            // Don't cache specific taxonomies
            if (!Factory::get('Cache\Frontend\Exceptions')->shouldTaxonomyBeCached()) {
                $this->cacheRequest = false;
            }

            // DONOTCACHEPAGE was defined
            if (Factory::get('Cache\Frontend\Exceptions')->doNotCachePageWasDefined()) {
                $this->cacheRequest = false;
            }
        }

        // Check if cacheRequest is still true
        if ($this->cacheRequest == true && Factory::get('Cache\Frontend\Cache')->isCacheTaskAvailable()) {
            $startBuffer = true;
        } else {
            // Fallback for fragmented caching
            if (Factory::get('Cache\Config')->get('fragmentCaching')) {
                $this->cacheRequestButDontSave = true;
                $startBuffer = true;
            }
        }

        // Start buffer
        if ($startBuffer) {
            // Last chance to skip this process
            if (Factory::get('Cache\Frontend\Compatibility')->shouldCachingBeSkipped() == false) {
                Factory::get('Cache\Frontend\Cache')->startBuffer();
            } else {
                /*
                    To make it easier for third party developers, we switch the cacheRequest / cacheRequestButDontSave status
                    to false when the compatibility class decides not to cache or process the current request.
                */
                $this->cacheRequest = false;
                $this->cacheRequestButDontSave = false;
            }
        }
    }

    /**
     * finishCaching function.
     *
     * @access public
     * @return void
     */
    public function finishCaching()
    {
        if (Factory::get('Cache\Frontend\Cache')->bufferActive == true) {
            // We use this technique only when fragmentCaching is active.
            // If it's not we wait until php shutdowns our ob_start(); buffer
            if (Factory::get('Cache\Config')->get('fragmentCaching')) {
                Factory::get('Cache\Frontend\Cache')->saveBuffer();

                // Only save data if page was intended to be cached
                if ($this->cacheRequestButDontSave == false) {
                    Factory::get('Cache\Frontend\Resolver')->savePageData();

                    Factory::get('Cache\Frontend\Cache')->endBuffer();

                    // We reset runtime in this case, because we load the cached file we have just created.
                    // It would distort the result, if we would calculate the processing time into the cache loading time.
                    // Factory::get('Cache\Init')->runtimeStart = microtime(1);

                    // Load cached page
                    Factory::get('Cache\Frontend\Cache')->loadCacheFile();
                } else {
                    // Send the non modified/cached page
                    Factory::get('Cache\Frontend\Cache')->endBuffer(true);
                }
            }
        }
    }

    /**
     * handleHeadRequest function.
     *
     * @access public
     * @param mixed $exit
     * @return void
     */
    public function handleHeadRequest($exit)
    {
        // If request is from OSO Super Cache Preloader, allow HEAD request and don't terminate the request
        if (!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'OSO-Super-Cache-Bot/') !== false) {
            $exit = false;
        }

        return $exit;
    }

    /**
     * handleCommentRequest function.
     *
     * @access public
     * @param mixed $location
     * @param mixed $comment
     * @return void
     */
    public function handleCommentRequest($location, $comment)
    {
        if (!empty($comment->comment_post_ID)) {
            // Force origin page to refresh cache
            Factory::get('Cache\Frontend\Garbage')->refreshCache($comment->comment_post_ID, 0, 0);
        }

        return $location;
    }
}
