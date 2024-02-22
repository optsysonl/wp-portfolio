<?php
/*
 * 
 */

namespace OSOSuperCache\Cache\Backend;

use OSOSuperCache\Factory;

class MetaBox
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

    protected function __construct()
    {
    }

    /**
     * registerMetaBox function.
     *
     * @access public
     * @return void
     */
    public function registerMetaBox()
    {
        add_meta_box(
            'oso-super-cache-meta-box',
            _x('OSO Super Cache', 'Meta box title', 'oso-super-cache'),
            [$this, 'displayMetaBox'],
            null,
            'side',
            'default',
            null
        );
    }

    /**
     * getDataForMetaBox function.
     *
     * @access public
     * @param mixed $postId
     * @return void
     */
    public function getDataForMetaBox ($postId)
    {
        $data = [
            'notCachedMessage'=>_x('This page has not been added by OSO Super Cache yet, but will be added automatically soon.', 'Meta box notice', 'oso-super-cache'),
            'refreshCacheMessage'=>_x('Cache will be refreshed or created as soon as possible.', 'Meta box notice', 'oso-super-cache'),
            'lastUpdated'=>'0000-00-00 00:00:00',
            'cachedPageData'=>[],
        ];

        // Detect if page is homepage
        if (get_option('show_on_front') == 'page') {

            $homepageId = get_option('page_on_front');

            if (!empty($homepageId) && $homepageId == $postId) {

                $data['cachedPageData'] = $this->getCachedData();

                // If multiple, find cached data with / url
                if (!empty($data['cachedPageData'])) {
                    foreach ($data['cachedPageData'] as $key => $pageData) {
                        if (!empty($pageData->url) && $pageData->url == '/') {
                            // Update entry with index 0
                            $data['cachedPageData'][0] = $pageData;
                        }
                    }
                }
            }
        }

        // If page is not homepage
        if (empty($data['cachedPageData'])) {
            $data['cachedPageData'] = $this->getCachedData($postId);
        }

        if (!empty($data['cachedPageData'][0]->last_updated) && $data['cachedPageData'][0]->last_updated != '0000-00-00 00:00:00') {
            $data['lastUpdated'] =  Factory::get('Cache\Tools')->formatTimestamp(strtotime($data['cachedPageData'][0]->last_updated));
        }

        return $data;
    }

    /**
     * displayMetaBox function.
     *
     * @access public
     * @param mixed $postData
     * @return void
     */
    public function displayMetaBox($postData)
    {
        $data = $this->getDataForMetaBox($postData->ID);

        include Factory::get('Cache\Backend\Backend')->templatePath.'/meta-box.html.php';
    }

    /**
     * saveMetaBoxData function.
     *
     * @access public
     * @param mixed $postId
     * @param mixed $post (default: null)
     * @param mixed $update (default: null)
     * @return void
     */
    public function saveMetaBoxData($postId, $post = null, $update = null)
    {
        global $wpdb;

        $user = wp_get_current_user();

        // Detect if page is homepage
        if (get_option('show_on_front') == 'page') {
            $homepageId = get_option('page_on_front');
        }

        // Check user rights again
        if (!empty($user->allcaps['publish_posts']) && !empty($_POST['oso-super-cache']['formSend'])) {
            $wpdb->query('
                UPDATE
                    `'.$wpdb->prefix.'oso_super_cache_pages`
                SET
                    `dont_cache`='.(isset($_POST['oso-super-cache']['dont_cache']) ? 1 : 0).'
                WHERE
                    '.(!empty($homepageId) && $homepageId == $postId ? '`is_home`=1' : '`post_id`='.intval($postId)).'
            ');
        }

        // Refresh cache if user forces to refresh it. The publish state will be ignored,
        // because sometimes a published page goes back to private or draft and we want to remove the cached version
        // Only perform these processes when the meta box was available to ensure, it is a valid page and not something like a price table (go_pricing)
        if (!empty($_POST['oso-super-cache']['metaBox']) || !empty($_POST['oso-super-cache']['quickEdit'])) {

            $refreshCache = 0;

            if (!empty($_POST['oso-super-cache']['refresh_cache'])) {
                $refreshCache = 1;
            }

            // Only modify cache if page is published
            if (!empty($post->post_status) && $post->post_status == 'publish') {
                // Check if published pages always refresh cache
                if (Factory::get('Cache\Config')->get('cacheRefreshCacheAfterPublish')) {
                    $refreshCache = 1;
                }
            }

            $postPermalink = get_permalink($postId);

            // Check if page was indexed as 404 and remove it
            if ($refreshCache || (!empty($post->post_status) && $post->post_status == 'publish')) {
                if (!empty($postPermalink)) {
                    $urlParts = parse_url($postPermalink);

                    if (!empty($urlParts['path'])) {
                        Factory::get('Cache\Frontend\Garbage')->remove404FromCache($urlParts['path']);
                    }
                }
            }

            // Check if QuickEdit Routine
            if (!empty($_POST['oso-super-cache']['quickEdit'])) {

                $refreshCache = 0;

                if (!empty($_POST['oso-super-cache']['refresh_cache'])) {
                    $refreshCache = 1;
                }
            }

            if ($refreshCache) {
                if (!empty($homepageId) && $homepageId == $postId) {
                    Factory::get('Cache\Frontend\Garbage')->refreshCache(0, 0, 1);
                } else {
                    Factory::get('Cache\Frontend\Garbage')->refreshCache($postId, 0, 0);
                }

                // Check if homepage should be refreshed
                if (Factory::get('Cache\Config')->get('cacheRefreshHomeCacheAfterPublish')) {
                    Factory::get('Cache\Frontend\Garbage')->refreshCache(0, 0, 1);
                }

                // Check if archives should be refreshed
                if (Factory::get('Cache\Config')->get('cacheRefreshArchiveCacheAfterPublish') && !empty($post->post_type)) {
                    Factory::get('Cache\Frontend\Garbage')->refreshCache(0, $post->post_type, 0);
                }

                // Check if feeds should be refreshed
                if (Factory::get('Cache\Config')->get('cacheRefreshFeedCacheAfterPublish')) {
                    Factory::get('Cache\Frontend\Garbage')->refreshCache(0, 0, 0, 1);
                }
            } else {
                if (!empty($post->post_status) && $post->post_status == 'publish' && empty($_POST['oso-super-cache']['quickEdit'])) {
                    // Check if homepage should be refreshed
                    if (Factory::get('Cache\Config')->get('cacheRefreshHomeCacheAfterPublish')) {
                        Factory::get('Cache\Frontend\Garbage')->refreshCache(0, 0, 1);
                    }

                    // Check if archives should be refreshed
                    if (Factory::get('Cache\Config')->get('cacheRefreshArchiveCacheAfterPublish')) {
                        Factory::get('Cache\Frontend\Garbage')->refreshCache(0, $post->post_type, 0);
                    }

                    // Check if feeds should be refreshed
                    if (Factory::get('Cache\Config')->get('cacheRefreshFeedCacheAfterPublish')) {
                        Factory::get('Cache\Frontend\Garbage')->refreshCache(0, 0, 0, 1);
                    }
                }
            }

            // Instant preload
            if ($refreshCache && !empty($post->post_status) && $post->post_status == 'publish') {
                Factory::get('Cache\Frontend\InstantPreloader')->preload($postPermalink);
            }
        }
    }

    /**
     * addPostColumn function.
     *
     * @access public
     * @param mixed $columns
     * @return void
     */
    public function addPostColumn ($columns)
    {
        $columns['oso_super_cache'] = "OSO Super Cache";

        return $columns;
    }

    /**
     * handlePostColumn function.
     *
     * @access public
     * @param mixed $column
     * @param mixed $id
     * @return void
     */
    public function handlePostColumn ($columnName, $postId)
    {
        if ($columnName === "oso_super_cache") {

            $data = $this->getDataForMetaBox($postId);

            if (empty($data['cachedPageData'][0])) {
                echo '<span class="dashicons dashicons-info" title="'.$data['notCachedMessage'].'"></span>';
            } else {
                if ($data['lastUpdated'] == '0000-00-00 00:00:00') {
                    echo '<span class="dashicons dashicons-update" title="'.$data['refreshCacheMessage'].'"></span>';
                } else {
                    echo $data['lastUpdated'];
                }
            }
        }
    }

    /**
     * displayQuickEditOption function.
     *
     * @access public
     * @param mixed $columnName
     * @param mixed $postType
     * @return void
     */
    public function displayQuickEditOption($columnName, $postType)
    {
        global $wpdb;

        if ($columnName === "oso_super_cache") {

            include Factory::get('Cache\Backend\Backend')->templatePath.'/quick-edit-option.html.php';
        }
    }

    /**
     * getCachedData function.
     *
     * @access public
     * @param int $postId (default: 0 == home)
     * @return void
     */
    public function getCachedData($postId = 0)
    {
        global $wpdb;

        $postId = intval($postId);

        // page_on_front - static page page ^_^
        // page_for_posts - static post page
        // show_on_front - page: page_on_front or if this is 0 then page_for_posts
        // show_on_front - posts

        $siteUrl = get_site_url();
        $https = parse_url($siteUrl)['scheme'] == 'https' ? 1 : 0;

        $cachedPost = $wpdb->get_results('
            SELECT
                `url`,
                `post_id`,
                `dont_cache`,
                `last_updated`
            FROM
                `'.$wpdb->prefix.'oso_super_cache_pages`
            WHERE
                '.(!empty($postId) ? '`post_id`="'.$postId.'"' : '`is_home`=1').'
                AND
                `https`='.$https.'
        ');

        return $cachedPost;
    }
}
