<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class Frontend
{

    private static $instance;

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

    public function __construct()
    {
        // Check if cache is activated, if not, do nothing
        if (Factory::get('Cache\Config')->cacheActivated()) {

            // Check if page is in cache or if page should be cached
            if (Factory::get('Cache\Config')->get('cacheLateInitialization') == false) {
                // Default, fast
                add_action('plugins_loaded', [Factory::get('Cache\Frontend\HandleRequest'), 'processPageRequest']);
            } elseif (Factory::get('Cache\Config')->get('cacheLateInitialization') == 'late') {
                // Later
                add_action('wp_loaded', [Factory::get('Cache\Frontend\HandleRequest'), 'processPageRequest']);
            } elseif (Factory::get('Cache\Config')->get('cacheLateInitialization') == 'super-late') {
                // Super late
                add_action('wp', [Factory::get('Cache\Frontend\HandleRequest'), 'processPageRequest'], 1);
            }

            // Allow OSO Super Cache Bot HEAD requests
            add_filter('exit_on_http_head', [Factory::get('Cache\Frontend\HandleRequest'), 'handleHeadRequest']);

            // Handle miscellaneous settings/optimizations
            add_action('init', [Factory::get('Cache\Frontend\Miscellaneous'), 'handleSettings'], PHP_INT_MAX);

            // Clean URL
            add_action('parse_request', [Factory::get('Cache\Frontend\Resolver'), 'cleanRequestedURL']);

            // Start Cache buffer
            add_action('wp', [Factory::get('Cache\Frontend\HandleRequest'), 'startCaching'], (PHP_INT_MAX-1));

            // Register lazy load scripts
            add_action('wp_enqueue_scripts', [Factory::get('Cache\Frontend\Images'), 'registerScript']);

            //TODO dequeue scripts styles
            add_action('wp_enqueue_scripts', [Factory::get('Cache\Frontend\Scripts'), 'excludeScripts'], 999);
            add_action('wp_enqueue_scripts', [Factory::get('Cache\Frontend\Styles'), 'excludeStyles'], 999);

            // Handle Feeds
            add_action('do_feed_rdf', [Factory::get('Cache\Frontend\Feed'), 'handleFeed'], 1, 2);
            add_action('do_feed_rss', [Factory::get('Cache\Frontend\Feed'), 'handleFeed'], 1, 2);
            add_action('do_feed_rss2', [Factory::get('Cache\Frontend\Feed'), 'handleFeed'], 1, 2);
            add_action('do_feed_atom', [Factory::get('Cache\Frontend\Feed'), 'handleFeed'], 1, 2);

            // Finish caching, save page infos, output buffer
            add_action('wp_footer', [Factory::get('Cache\Frontend\HandleRequest'), 'finishCaching'], (PHP_INT_MAX));

            // Handle API requests
            add_filter('query_vars', [Factory::get('Cache\API'), 'addVars'], 0);
            add_filter('parse_request', [Factory::get('Cache\API'), 'detectRequests'], 0);

            // Handle comments
            if (Factory::get('Cache\Config')->get('cacheRefreshCacheAfterComment')) {
                add_filter('comment_post_redirect', [Factory::get('Cache\Frontend\HandleRequest'), 'handleCommentRequest'], 1, 2);
            }

            // Handle scheduled posts
            add_action('publish_future_post', [$this, 'handleScheduledPost']);
        }
        $this->setBypassCookie();
    }

    public function setBypassCookie(){
        if ( version_compare( phpversion(), '7.3', '>=' ) ) {
            setcookie(
                'wpSGCacheBypass',
                1,
                array(
                    'expires'  => 0,
                    'path'     => '/',
                    'httponly' => true,
                    'samesite' => 'Lax',
                )
            );
        } else {
            setcookie( 'wpSGCacheBypass', 1, 0, '/' . ';samesite=Lax;' );
        }
    }

    public function handleScheduledPost($postId)
    {

        global $wpdb;

        // Check if published pages always refresh cache
        if (Factory::get('Cache\Config')->get('cacheRefreshCacheAfterPublish')) {
        }

        // Check if homepage should be refreshed
        if (Factory::get('Cache\Config')->get('cacheRefreshHomeCacheAfterPublish')) {
            Factory::get('Cache\Frontend\Garbage')->refreshCache(0, 0, 1);
        }

        // Check if archives should be refreshed
        if (Factory::get('Cache\Config')->get('cacheRefreshArchiveCacheAfterPublish')) {
            // Get post type
            $postTypeResult = $wpdb->get_results('
                SELECT
                    `post_type`
                FROM
                    `'.$wpdb->posts.'`
                WHERE
                    `ID`='.intval($postId).'
            ');

            if (!empty($postTypeResult[0]->post_type)) {
                Factory::get('Cache\Frontend\Garbage')->refreshCache(0, $postTypeResult[0]->post_type, 0);
            }
        }
    }
}
