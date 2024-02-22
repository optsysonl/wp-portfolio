<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class Exceptions
{
    private static $instance;

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

    protected function __construct()
    {
    }

    // When a page is requested, this method checks, if the page should be cached
    public function shouldPageBeCached()
    {
        $shouldCache = false;

        // We don't cache pages from logged in users. But we may delivery already cached pages for them.
        if (!is_user_logged_in() || Factory::get('Cache\Config')->get('loggedInUserGetCachedPages')) {
            $shouldCache = true;
        }

        $cachedPageData = Factory::get('Cache\Frontend\Resolver')->getCachedPageData();

        // Check, if the requested page is known and should not be cached
        if ($shouldCache && !empty($cachedPageData->dont_cache)) {
            $shouldCache = false;
        }

        // Check, if the requested page contains query strings
        if ($shouldCache && !empty(Factory::get('Cache\Frontend\Resolver')->getURLInfo('query'))) {
            $queryVars = [];
            parse_str(Factory::get('Cache\Frontend\Resolver')->getURLInfo('query'), $queryVars);

            // Don't cache page, when query string is set and option is deactivated
            if (empty(Factory::get('Cache\Config')->get('cachePagesWithQueryStrings'))) {
                $shouldCache = false;
            }

            // Cache search results
            if (!empty(Factory::get('Cache\Config')->get('cacheSearchResults')) && !empty($queryVars['s'])) {
                $shouldCache = true;
            }

            // Don't cache pages when a specific query var is given
            if (!empty(Factory::get('Cache\Config')->get('cacheDontCachePagesContainQuery'))) {
                $blacklistedQuerys = Factory::get('Cache\Config')->get('cacheDontCachePagesContainQuery');

                // If we find a blacklisted key, we don't cache the request
                if ($this->findIdenticalKeysInArray($blacklistedQuerys, $queryVars)) {
                    $shouldCache = false;
                }
            }
        }

        // Check, if the requested page contains a path
        $blacklistedPathes = Factory::get('Cache\Config')->get('cacheDontCachePagesContainPath');

        if ($shouldCache && !empty($blacklistedPathes)) {
            $currentPath = Factory::get('Cache\Frontend\Resolver')->getURLInfo('path');

            if (!empty($currentPath)) {
                foreach ($blacklistedPathes as $path) {
                    if (preg_match('/'.addcslashes($path, '/\'').'/', $currentPath)) {
                        $shouldCache = false;
                    }
                }
            }
        }

        // Check user agent
        $blacklistUserAgents = Factory::get('Cache\Config')->get('cacheDontUseCacheWhenUserAgent');

        if ($shouldCache) {
            if (!empty($_SERVER['HTTP_USER_AGENT'])) {
                $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);

                foreach ($blacklistUserAgents as $blacklistUserAgent) {
                    if (strpos($userAgent, strtolower($blacklistUserAgent)) !== false) {
                        $shouldCache = false;

                        break;
                    }
                }
            }
        }

        // Check cookie
        $blacklistCookies = Factory::get('Cache\Config')->get('cacheDontUseCacheWhenCookie');

        if ($shouldCache) {
            if (!empty($_COOKIE)) {
                foreach ($blacklistCookies as $cookieKey) {
                    if (!empty($_COOKIE[$cookieKey])) {
                        $shouldCache = false;

                        break;
                    }
                }
            }
        }

        /*
            When commenter data is available, we don't create a cache file because the users data could be saved
            into input fields and appear in the cached file.
        */
        if ($shouldCache) {
            $commenterData = wp_get_current_commenter();

            if (!empty($commenterData['comment_author']) || !empty($commenterData['comment_author_email']) || !empty($commenterData['comment_author_url'])) {
                $shouldCache = false;
            }
        }

        // If user is logged in and preview mode is active, dont cache
        if ($shouldCache) {
            if (is_user_logged_in() && (!empty($_GET['preview']) || !empty($_GET['preview_id']))) {
                $shouldCache = false;
            }
        }

        return $shouldCache;
    }

    /**
     * shouldPostTypeBeCached function.
     *
     * @access public
     * @param mixed $postType (default: null)
     * @return void
     */
    public function shouldPostTypeBeCached($postType = null)
    {
        if (empty($postType)) {
            $postType = Factory::get('Cache\Frontend\Resolver')->getPostType();
        }

        $shouldCache = true;

        if (!empty($postType)) {
            $blacklistedPostTypes = Factory::get('Cache\Config')->get('cacheDontCachePagesOfPostType');

            if (!empty($blacklistedPostTypes)) {
                if (in_array($postType, $blacklistedPostTypes)) {
                    $shouldCache = false;
                }
            }
        }

        return $shouldCache;
    }

    /**
     * shouldTaxonomyBeCached function.
     *
     * @access public
     * @param mixed $taxonomy (default: null)
     * @return void
     */
    public function shouldTaxonomyBeCached($taxonomy = null)
    {
        if (empty($taxonomy)) {
            $taxonomy = Factory::get('Cache\Frontend\Resolver')->getTaxonomy();
        }

        $shouldCache = true;

        if (!empty($taxonomy)) {
            $blacklistedTaxonomies = Factory::get('Cache\Config')->get('cacheDontCachePagesOfTaxonomy');

            if (!empty($blacklistedTaxonomies)) {
                if (in_array($taxonomy, $blacklistedTaxonomies)) {
                    $shouldCache = false;
                }
            }
        }

        return $shouldCache;
    }

    public function isUserLoggedIn()
    {
        $loggedIn = false;

        if (function_exists('is_user_logged_in')) {
            $loggedIn = is_user_logged_in();
        } else {
            // If we don't know, if the user is logged in or not, it's better not to cache the page
            $loggedIn = true;
        }

        return $loggedIn;
    }

    public function isCacheLifetimeOver()
    {
        $lifetimeOver = false;

        $cachedPageData = Factory::get('Cache\Frontend\Resolver')->getCachedPageData();

        if (isset($cachedPageData->last_updated)) {
            if ($cachedPageData->last_updated == '0000-00-00 00:00:00') {
                $lifetimeOver = true;
            } else {
                $lastUpdatedTimestamp = strtotime($cachedPageData->last_updated, time());

                $maxLifetimeTimestamp = Factory::get('Cache\Frontend\Resolver')->getMaxLifetimeTimestamp();

                if ($lastUpdatedTimestamp < $maxLifetimeTimestamp) {
                    $lifetimeOver = true;
                }
            }
        } else {
            /*
                Sometimes, a cached page can lie in the cache folder, but has no entry in the index.
                To avoid issues with old settings for the cached page, we force to refresh the page.
            */
            $lifetimeOver = true;
        }

        return $lifetimeOver;
    }

    public function findIdenticalKeysInArray($arrayA, $arrayB)
    {
        $foundIdenticalKey = false;

        if (!empty($arrayA)) {
            foreach ($arrayA as $key => $value) {
                // Break loop when an identical key was found
                if ($foundIdenticalKey) {
                    break;
                }

                if (isset($arrayB[$key])) {
                    if (is_array($value) && !empty($value)) {
                        $foundIdenticalKey = $this->findIdenticalKeysInArray($arrayA[$key], $arrayB[$key]);
                    } else {
                        $foundIdenticalKey = true;
                    }
                }
            }
        }

        return $foundIdenticalKey;
    }

    /**
     * doNotCachePageWasDefined function.
     *
     * @access public
     * @return void
     */
    public function doNotCachePageWasDefined()
    {
        $doNotCache = false;

        if (defined('DONOTCACHEPAGE') && DONOTCACHEPAGE == true) {
            $doNotCache = true;
        }

        return $doNotCache;
    }
}
