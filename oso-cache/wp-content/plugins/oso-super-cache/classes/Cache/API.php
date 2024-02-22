<?php
/*
 *
 */

namespace OSOSuperCache\Cache;

use OSOSuperCache\Factory;

class API
{

    private static $instance;

    private $updateURL = 'https://software.osobrand.net/plugins/oso-super-cache';

    private $response = [];

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
     * addVars function.
     *
     * @access public
     * @param mixed $vars
     * @return void
     */
    public function addVars($vars)
    {
        $vars[] = '__osoSuperCacheCall';

        return $vars;
    }

    /**
     * detectRequests function.
     *
     * @access public
     * @return void
     */
    public function detectRequests()
    {
        global $wp;

        if (!empty($wp->query_vars['__osoSuperCacheCall'])) {

            $data = json_decode(file_get_contents("php://input"));

            $this->handleRequest($wp->query_vars['__osoSuperCacheCall'], $data);

            exit;
        }
    }

    /**
     * handleRequest function.
     *
     * @access public
     * @param mixed $call
     * @param mixed $token
     * @param mixed $data
     * @return void
     */
    public function handleRequest($call, $data)
    {
        if ($call == 'getPreloadData') {

            $this->getPreloadData($data);

        } elseif ($call == 'performMaintenance') {

            $this->performMaintenance();
        }
    }

    /**
     * getPreloadData function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function getPreloadData($data)
    {
        global $wpdb;

        // Save preload stats from OSO Super Cache Preloader
        update_option('OSOSuperCachePreloadedStats', $data->preloadStats, 'no');

        // Save timestamp of this request
        update_option('OSOSuperCachePreloadedLastRequest', ['timestamp' => time()], 'no');

        // Detect if blog is configured for http or https.
        // We only preload the configured scheme not the duplicate content
        $url = get_site_url();
        $https = parse_url($url)['scheme'] == 'https' ? 1 : 0;

        // Get info about total cached pages and their runtime so OSO Super Cache can calculate how much pages should be run in one preload cycle
        $cacheInfoTotalPages = $wpdb->get_results('
            SELECT
                COUNT(*) as `total_pages_cached`
            FROM
                `' . $wpdb->prefix . 'oso_super_cache_pages`
            WHERE
                `dont_cache`=0
                AND
                `is_404`=0
                AND
                `https`=' . $https . '
        ');

        $cacheInfoAvgRuntimes = $wpdb->get_results('
            SELECT
                AVG(`runtime_without_cache`) as `average_runtime_without_cache`,
                AVG(`runtime_with_cache`) as `average_runtime_with_cache`
            FROM
                `' . $wpdb->prefix . 'oso_super_cache_pages`
            WHERE
                `dont_cache`=0
                AND
                `is_404`=0
                AND
                `https`=' . $https . '
                AND
                `runtime_with_cache`>0
        ');

        // Get next 100 pages which should be preloaded
        $preloadPages = $wpdb->get_results('
            SELECT
                `domain`,
                `hash`,
                `https`,
                `prefix`,
                `url`,
                IF(`next_update`="0000-00-00 00:00:00", NOW(), `next_update`) as "next_update",
                `is_archive`
            FROM
                `' . $wpdb->prefix . 'oso_super_cache_pages`
            WHERE
                `dont_cache`=0
                AND
                `is_404`=0
                AND
                `https`=' . $https . '
            ORDER BY
                `next_update` ASC
            LIMIT 0,250
        ');

        echo json_encode([
            'cacheInformation' => [
                'totalPagesCached' => $cacheInfoTotalPages[0]->total_pages_cached,
                'avgRuntimeWithoutCache' => $cacheInfoAvgRuntimes[0]->average_runtime_without_cache,
                'avgRuntimeWithCache' => $cacheInfoAvgRuntimes[0]->average_runtime_with_cache,
            ],
            'settings' => [
                'cacheActivated' => Factory::get('Cache\Config')->get('cacheActivated'),
                'preloaderActivated' => Factory::get('Cache\Config')->get('preloaderActivated'),
                'maxSimultaneousTasks' => Factory::get('Cache\Config')->get('maxSimultaneousTasks'),
                'cron' => Factory::get('Cache\Config')->get('cacheCronService'),
                'cronInterval' => Factory::get('Cache\Config')->get('cacheCronInterval'),
            ],
            'system' => [
                'currentTime' => Factory::get('Cache\Tools')->getDBTime(),
                'slug' => OSO_SUPER_CACHE_SLUG,
            ],
            'preload' => $preloadPages,
        ]);
    }

    /**
     * performMaintenance function.
     *
     * @access public
     * @return void
     */
    public function performMaintenance()
    {
        // Clear page cache
        Factory::get('Cache\Frontend\Garbage')->clearPageCache();

        // Remove old 404 entries from index
        Factory::get('Cache\Frontend\Garbage')->remove404Entries();

        echo json_encode([
            'success' => true,
        ]);
    }

    /**
     * getPluginInformation function.
     *
     * @access public
     * @return void
     */
    public function getPluginInformation()
    {
        $response = wp_remote_post(
            $this->updateURL.'/index.php',
            [
                'timeout'   =>45,
                'body'      =>[
                    'version'=>OSO_SUPER_CACHE_VERSION,
                    'product'=>(defined('OSO_SUPER_CACHE_DEV_BUILD') && OSO_SUPER_CACHE_DEV_BUILD == true ? 'dev-' : '').dirname(OSO_SUPER_CACHE_SLUG),
                ]
            ]
        );

        if (!empty($response) && is_array($response) && !empty($response['body'])) {
            $body = json_decode($response['body']);

            if (!empty($body->success) && !empty($body->pluginInformation)) {
                return unserialize($body->pluginInformation);
            }
        }
    }

    /**
     * getLatestVersion function.
     *
     * @access public
     * @return void
     */
    public function getLatestVersion()
    {
        $response = wp_remote_post(
            $this->updateURL.'/index.php',
            [
                'timeout'   =>45,
                'body'      =>[
                    'version'=>OSO_SUPER_CACHE_VERSION,
                    'product'=>(defined('OSO_SUPER_CACHE_DEV_BUILD') && OSO_SUPER_CACHE_DEV_BUILD == true ? 'dev-' : '').dirname(OSO_SUPER_CACHE_SLUG),
                ],
            ]
        );

        if (!empty($response) && is_array($response) && !empty($response['body'])) {
            $body = json_decode($response['body'], false);
            $body->updateInformation->icons = json_decode(json_encode($body->updateInformation->icons), true);
            $body->updateInformation->banners = json_decode(json_encode($body->updateInformation->banners), true);
            $body->updateInformation->banners_rtl = json_decode(json_encode($body->updateInformation->banners_rtl), true);

            if (!empty($body->success) && !empty($body->updateInformation)) {
                return $body->updateInformation;
            }
        }
    }


}
