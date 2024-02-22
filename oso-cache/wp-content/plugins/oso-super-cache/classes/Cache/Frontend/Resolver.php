<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class Resolver
{
    private static $instance;

    private $customPrefix;
    private $requestedDomain;
    private $requestedURL;
    private $urlInfo;
    private $https;

    private $unknownQueryVar;

    protected $postId;
    protected $postType;
    protected $taxonomy;
    protected $term;
    protected $conditions;

    protected $cachedData;

    public static function getInstance ()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->requestedDomain  = $_SERVER['SERVER_NAME'];
        $this->requestedURL     = $_SERVER['REQUEST_URI'];
        $this->urlInfo          = parse_url($this->requestedURL);

        // We have to rebuild the URL due issues with empty keys
        $queries = [];
        if (!empty($this->urlInfo['query'])) {
            parse_str(rawurldecode($this->urlInfo['query']), $queries);
        }

        $this->requestedURL = $this->urlInfo['path'].(!empty($queries) ? '?'.http_build_query($queries) : '');

        $this->https        = Factory::get('Cache\Tools')->isHttps();
        $this->blogId       = get_current_blog_id();
    }

    /**
     * getCurrentBlogId function.
     *
     * @access public
     * @return void
     */
    public function getCurrentBlogId()
    {
        return $this->blogId;
    }

    /**
     * getRequestedDomain function.
     *
     * @access public
     * @return void
     */
    public function getRequestedDomain()
    {
        return $this->requestedDomain;
    }

    /**
     * cleanRequestedURL function.
     *
     * This prevents that a page is cached which has an unknown query var.
     * We do this, to avoid that some bad person makes infinite requests with unknown querys
     * to create thousands of files.
     *
     * @access public
     * @param mixed $queryVars
     * @return void
     */
    public function cleanRequestedURL($queryVars)
    {
        // Fix requestedURL
        $query = [];

        if (!empty($this->urlInfo['query'])) {
            parse_str($this->urlInfo['query'], $query);
        }

        $queryWithKnownVars = [];

        // Get all values which are known
        foreach ($queryVars->public_query_vars as $knownVar) {
            if (isset($query[$knownVar])) {
                $queryWithKnownVars[$knownVar] = $query[$knownVar];
            }
        }

        // Detect if an unkown query var was requested, if yes: don't cache page
        if (count($query) > count($queryWithKnownVars)) {
            $this->unknownQueryVar = true;
        }

        $this->requestedURL = $this->urlInfo['path'].(!empty($queryWithKnownVars) ? '?'.http_build_query($queryWithKnownVars) : '');

        return $queryVars;
    }

    /**
     * getHash function.
     *
     * @access public
     * @return void
     */
    public function getHash()
    {
        return $this->hashURL($this->customPrefix.$this->requestedDomain.$this->requestedURL);
    }

    /**
     * hashURL function.
     *
     * @access public
     * @param mixed $url
     * @return void
     */
    public function hashURL($url)
    {
        return sha1($url);
    }

    /**
     * isHTTPS function.
     *
     * @access public
     * @return void
     */
    public function isHTTPS()
    {
        return $this->https;
    }

    /**
     * isUnknownQueryVarPresent function.
     *
     * @access public
     * @return void
     */
    public function isUnknownQueryVarPresent()
    {
        if ($this->unknownQueryVar === true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * updateRuntimeWithCache function.
     *
     * @access public
     * @return void
     */
    public function updateRuntimeWithCache()
    {
        global $wpdb;

        $runtimeWithCache = Factory::get('Cache\Init')->getTotalRuntime();

        if ($runtimeWithCache < 0) {
            // We need a value for runtime_with_cache, otherwise the cache preloader wouldn't fetch the page.
            // This usually happens only when we create and load the cache in the same run.
            $runtimeWithCache = 0.100;
        }

        $wpdb->query('
            UPDATE
                `'.$wpdb->prefix.'oso_super_cache_pages`
            SET
                `runtime_with_cache`="'.$runtimeWithCache.'"
            WHERE
                `hash`="'.$this->getHash().'"
                AND
                `https`="'.($this->isHTTPS() ? 1 : 0).'"
        ');
    }

    /**
     * getCachedPageData function.
     *
     * @access public
     * @return void
     */
    public function getCachedPageData()
    {
        global $wpdb;

        if (!empty($this->cachedData[$this->getHash()])) {
            return $this->cachedData[$this->getHash()];
        } else {
            $pageDataResult = $wpdb->get_results('
                SELECT
                    `post_id`,
                    `post_type`,
                    `taxonomy`,
                    `term`,
                    `conditions`,
                    `dont_cache`,
                    `is_home`,
                    `is_archive`,
                    `is_feed`,
                    `is_404`,
                    `last_updated`,
                    `next_update`
                FROM
                    `'.$wpdb->prefix.'oso_super_cache_pages`
                WHERE
                    `hash`="'.$wpdb->_escape($this->getHash()).'"
                    AND
                    `https`="'.($this->isHTTPS() ? 1 : 0).'"
            ');

            if (!empty($pageDataResult[0])) {
                $pageDataResult[0]->conditions = (object) unserialize($pageDataResult[0]->conditions);

                $this->cachedData[$this->getHash()] = $pageDataResult[0];

                // Save info to GLOBALS so that third party developers can access them
                $GLOBALS['OSOSuperCache'] = ['cachedPageData'=>$this->cachedData[$this->getHash()]];

                $this->conditions = $pageDataResult[0]->conditions;

                return $this->cachedData[$this->getHash()];
            } else {
                return false;
            }
        }
    }

    /**
     * collectPageData function.
     *
     * @access public
     * @return void
     */
    public function collectPageData()
    {
        global $post, $wp_query;

        $this->postId   = 0;
        $this->postType = '-';
        $this->taxonomy = '';
        $this->term     = '';

        $this->conditions = (object) [
            'is_home'=>is_home(),
            'is_front_page'=>is_front_page(),
            'is_single'=>is_single(),
            'is_sticky'=>is_sticky(),
            'is_post_type_archive'=>is_post_type_archive(),
            'comments_open'=>(is_single() ? comments_open() : false),
            'pings_open'=>(is_single() ? pings_open() : false),
            'is_page'=>is_page(),
            'is_page_template'=>is_page_template(),
            'is_category'=>is_category(),
            'is_tag'=>is_tag(),
            'is_tax'=>is_tax(),
            'is_author'=>is_author(),
            'is_date'=>is_date(),
            'is_year'=>is_year(),
            'is_month'=>is_month(),
            'is_day'=>is_day(),
            'is_time'=>is_time(),
            'is_archive'=>is_archive(),
            'is_search'=>is_search(),
            'is_404'=>is_404(),
            'is_paged'=>is_paged(),
            'is_attachment'=>is_attachment(),
            'is_feed'=>is_feed(),
            'is_preview'=>is_preview(),
            'is_rtl'=>is_rtl(),
            'is_multisite'=>is_multisite(),
            'is_main_site'=>is_main_site(),
            'is_child_theme'=>is_child_theme(),
        ];

        // Default homepage - no static page or post
        if (is_front_page() && is_home()) {
            $this->postId   = 0;

        // A static page
        } elseif (is_front_page()) {
            $this->postId   = 0;
            $this->postType = '-';

        // A static post
        } elseif (is_home()) {
            $this->postId   = 0;
            $this->postType = '-';
        } else {
            // Single post, single page
            if (is_single() || is_page()) {
                $this->postId   = !empty($post->ID) ? $post->ID : 0;
                $this->postType = !empty($post->post_type) ? $post->post_type : '-';
            } elseif (is_archive()) {
                if (!empty($wp_query->query_vars['category_name'])) {
                    $this->taxonomy = 'category';
                    $this->term     = $wp_query->query_vars['category_name'];
                } elseif (!empty($wp_query->query_vars['tag'])) {
                    $this->taxonomy = 'tag';
                    $this->term     = $wp_query->query_vars['tag'];
                } else {
                    $this->postType = isset($wp_query->query_vars['post_type']) ? $wp_query->query_vars['post_type'] : '-';
                    $this->taxonomy = isset($wp_query->query_vars['taxonomy']) ? $wp_query->query_vars['taxonomy'] : '';
                    $this->term     = isset($wp_query->query_vars['term']) ? $wp_query->query_vars['term'] : '';
                }
            }
        }

        // Fallback for first run, if 3rd party devs need the data
        if ($this->getCachedPageData() == false) {
            $this->cachedData[$this->getHash()] = (object) [
                'post_id'=>$this->postId,
                'post_type'=>$this->postType,
                'taxonomy'=>$this->taxonomy,
                'term'=>$this->term,
                'conditions'=>$this->conditions,
                'dont_cache'=>0,
                'is_home'=>$this->conditions->is_home || $this->conditions->is_front_page ? 1 : 0,
                'is_archive'=>$this->conditions->is_archive ? 1 : 0,
                'is_feed'=>$this->conditions->is_feed ? 1 : 0,
                'is_404'=>$this->conditions->is_404 ? 1 : 0,
            ];
        }
    }

    /**
     * getPostType function.
     *
     * @access public
     * @return void
     */
    public function getPostType()
    {
        return $this->postType;
    }

    /**
     * getTaxonomy function.
     *
     * @access public
     * @return void
     */
    public function getTaxonomy()
    {
        return $this->taxonomy;
    }

    /**
     * savePageData function.
     *
     * @access public
     * @return void
     */
    public function savePageData()
    {
        global $wpdb;

        $wpdb->query('
            INSERT INTO
                `'.$wpdb->prefix.'oso_super_cache_pages`
            (
                `domain`,
                `hash`,
                `https`,
                `prefix`,
                `url`,
                `post_id`,
                `post_type`,
                `taxonomy`,
                `term`,
                `conditions`,
                `dont_cache`,
                `is_home`,
                `is_archive`,
                `is_feed`,
                `is_404`,
                `runtime_without_cache`,
                `last_updated`
            )
            VALUES
            (
                "'.$wpdb->_escape($this->requestedDomain).'",
                "'.$this->getHash().'",
                "'.($this->isHTTPS() ? 1 : 0).'",
                "'.$wpdb->_escape($this->customPrefix).'",
                "'.$wpdb->_escape($this->requestedURL).'",
                "'.$wpdb->_escape($this->postId).'",
                "'.$wpdb->_escape($this->postType).'",
                "'.$wpdb->_escape($this->taxonomy).'",
                "'.$wpdb->_escape($this->term).'",
                "'.$wpdb->_escape(serialize($this->conditions)).'",
                0,
                '.($this->conditions->is_home || $this->conditions->is_front_page ? 1 : 0).',
                '.($this->conditions->is_archive ? 1 : 0).',
                '.($this->conditions->is_feed ? 1 : 0).',
                '.($this->conditions->is_404 ? 1 : 0).',
                "'.Factory::get('Cache\Init')->getTotalRuntime().'",
                NOW()
            )
            ON DUPLICATE KEY UPDATE
                `last_updated`=NOW(),
                `post_id`="'.$wpdb->_escape($this->postId).'",
                `post_type`="'.$wpdb->_escape($this->postType).'",
                `taxonomy`="'.$wpdb->_escape($this->taxonomy).'",
                `term`="'.$wpdb->_escape($this->term).'",
                `conditions`="'.$wpdb->_escape(serialize($this->conditions)).'",
                `is_home`='.($this->conditions->is_home || $this->conditions->is_front_page ? 1 : 0).',
                `is_archive`='.($this->conditions->is_archive ? 1 : 0).',
                `is_feed`='.($this->conditions->is_feed ? 1 : 0).',
                `is_404`='.($this->conditions->is_404 ? 1 : 0).',
                `runtime_without_cache`="'.Factory::get('Cache\Init')->getTotalRuntime().'"
        ');

        // Set next_update timestamp
        $wpdb->query('
            UPDATE
                `'.$wpdb->prefix.'oso_super_cache_pages`
            SET
                `next_update`=FROM_UNIXTIME(UNIX_TIMESTAMP() + '.$this->getMaxLifetime().')
            WHERE
                `hash`="'.$this->getHash().'"
                AND
                `https`="'.($this->isHTTPS() ? 1 : 0).'"
        ');
    }

    /**
     * getCondition function.
     *
     * @access public
     * @param mixed $condition
     * @return void
     */
    public function getCondition($condition)
    {
        if (isset($this->conditions->{$condition})) {
            return $this->conditions->{$condition};
        } else {
            return false;
        }
    }

    /**
     * getURLInfo function.
     *
     * @access public
     * @param string $key (default: '')
     * @return void
     */
    public function getURLInfo($key = '')
    {
        if (!empty($key)) {
            if (!empty($this->urlInfo[$key])) {
                return $this->urlInfo[$key];
            } else {
                return false;
            }
        } else {
            return $this->urlInfo;
        }
    }

    /**
     * getMaxLifetimeTimestamp function.
     *
     * @access public
     * @return void
     */
    public function getMaxLifetimeTimestamp()
    {
        global $wpdb;

        $cachedPageData = $this->getCachedPageData();

        $cacheLifetimes = Factory::get('Cache\Config')->get('cacheLifetime');

        // Get current time from mysql db
        $currentMySQLTime = $wpdb->get_results('SELECT NOW() as "currentDBTime"');

        $maxLifetimeTimestamp = strtotime($currentMySQLTime[0]->currentDBTime, time());

        // Home
        if (!empty($cachedPageData->is_home)) {
            $maxLifetimeTimestamp = $maxLifetimeTimestamp - $cacheLifetimes['home'];
        } elseif ($cachedPageData->is_archive) {
            // Check if post type specific cache times
            if (!empty($cachedPageData->post_type) && !empty($cacheLifetimes['archives'][$cachedPageData->post_type])) {
                $maxLifetimeTimestamp = $maxLifetimeTimestamp - $cacheLifetimes['archives'][$cachedPageData->post_type];
            } else {
                $maxLifetimeTimestamp = $maxLifetimeTimestamp - $cacheLifetimes['postType']['-'];
            }
        } elseif ($cachedPageData->is_feed) {
            // Feed cachetime
            $maxLifetimeTimestamp = $maxLifetimeTimestamp - $cacheLifetimes['feed'];
        } else {
            // Pages, posts, categories etc.
            if (!empty($cachedPageData->post_type) && !empty($cacheLifetimes['postType'][$cachedPageData->post_type])) {
                $maxLifetimeTimestamp = $maxLifetimeTimestamp - $cacheLifetimes['postType'][$cachedPageData->post_type];
            } else {
                $maxLifetimeTimestamp = $maxLifetimeTimestamp - $cacheLifetimes['postType']['-'];
            }
        }

        return $maxLifetimeTimestamp;
    }

    /**
     * getMaxLifetime function.
     *
     * @access public
     * @return void
     */
    public function getMaxLifetime()
    {
        $cachedPageData = $this->getCachedPageData();

        $cacheLifetimes = Factory::get('Cache\Config')->get('cacheLifetime');

        $maxLifetime = 0;

        // Home
        if (!empty($cachedPageData->is_home)) {
            $maxLifetime = $cacheLifetimes['home'];
        } elseif ($cachedPageData->is_archive) {
            // Check if post type specific cache times
            if (!empty($cachedPageData->post_type) && !empty($cacheLifetimes['archives'][$cachedPageData->post_type])) {
                $maxLifetime = $cacheLifetimes['archives'][$cachedPageData->post_type];
            } else {
                $maxLifetime = $cacheLifetimes['postType']['-'];
            }
        } elseif ($cachedPageData->is_feed) {
            // Feed cachetime
            $maxLifetime = $cacheLifetimes['feed'];
        } else {
            // Pages, posts, categories etc.
            if (!empty($cachedPageData->post_type) && !empty($cacheLifetimes['postType'][$cachedPageData->post_type])) {
                $maxLifetime = $cacheLifetimes['postType'][$cachedPageData->post_type];
            } else {
                $maxLifetime = $cacheLifetimes['postType']['-'];
            }
        }

        return $maxLifetime;
    }

    /**
     * setCustomPrefix function.
     *
     * @access public
     * @param mixed $prefix limited to 16 chars
     * @return void
     */
    public function setCustomPrefix($prefix)
    {
        if (strlen($prefix) > 16) {
            $prefix = substr($prefix, 0, 16);
        }

        $prefix = preg_replace('/[^A-Za-z0-9_\-]?/', '', $prefix);

        $this->customPrefix = $prefix;
    }
}
