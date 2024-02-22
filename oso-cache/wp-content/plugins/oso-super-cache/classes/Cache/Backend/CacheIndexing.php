<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Backend;

use OSOSuperCache\Factory;

class CacheIndexing
{
    private static $instance;

    private $imagePath;

    private $collectedURLs = [];

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
        // Load inactive config
        Factory::get('Cache\Config')->loadConfig('inactive');
    }

    /**
     * display function.
     *
     * @access public
     * @return void
     */
    public function display()
    {
        $this->imagePath = plugins_url('images', realpath(__DIR__.'/../../'));

        if (!empty($_POST['formSend']) && check_admin_referer('oso_super_cache_cache_indexing')) {

            // Reset sitemap index file info
            if (!empty($_POST['xmlSitemapIndexReset'])) {
                delete_option('OSOSuperCacheXMLSitemapIndexFiles');
            }

            // Reset sitemap info
            if (!empty($_POST['xmlSitemapReset'])) {
                delete_option('OSOSuperCacheXMLSitemapURLs');
            }

            // Load xml sitemap
            if (!empty($_POST['xmlSitemapURL'])) {
                $this->loadSitemap($_POST['xmlSitemapURL']);
            }
        }

        $inputXMLSitemapURL = esc_html(!empty($_POST['xmlSitemapURL']) ? stripslashes($_POST['xmlSitemapURL']) : rtrim(get_site_url(), '/').'/sitemap.xml');

        $xmlSitemapIndexURLs = get_option('OSOSuperCacheXMLSitemapIndexFiles', false);

        $xmlSitemapURLs = get_option('OSOSuperCacheXMLSitemapURLs', false);

        include Factory::get('Cache\Backend\Backend')->templatePath.'/cache-indexing.html.php';
    }

    /**
     * loadSitemap function.
     *
     * @access public
     * @param mixed $url
     * @return void
     */
    public function loadSitemap($url)
    {
        // 1. Check if sitemap extension is .xml and not .gz
        $urlInfo = pathinfo($url);

        if (empty($urlInfo['extension']) || $urlInfo['extension'] !== 'xml') {
            Factory::get('Cache\Backend\Backend')->addMessage(_x('Could not detect XML-Sitemap.', 'Status message', 'oso-super-cache'), 'error');
        } else {

            // 2. Load XML
            $args = [
                'timeout'=>60,
            ];

            $response = wp_remote_get($url, $args);

            if (!empty($response) && $response['response']['code'] == 200) {

                // Collect URLs
                preg_replace_callback('/\<loc\>(\<\!\[CDATA\[)?([^>]+)(\]\]\>)?\<\/loc\>/', [$this, 'collectURLs'], $response['body']);

                // Check if sitemap is sitemap index file
                if (strpos($response['body'], '<sitemapindex')) {

                    asort($this->collectedURLs);

                    update_option('OSOSuperCacheXMLSitemapIndexFiles', $this->collectedURLs, 'no');

                    Factory::get('Cache\Backend\Backend')->addMessage(_x('Your given XML Sitemap is a sitemap index file which is not supported.', 'Status message', 'oso-super-cache'), 'error');
                } else {
                    update_option('OSOSuperCacheXMLSitemapURLs', ['xml-sitemap'=>$urlInfo['basename'], 'urls'=>$this->collectedURLs], 'no');

                    Factory::get('Cache\Backend\Backend')->addMessage(_x('XML Sitemap loaded successfully.', 'Status message', 'oso-super-cache'), 'success');
                }
            } else {
                Factory::get('Cache\Backend\Backend')->addMessage(_x('Could not load XML Sitemap.', 'Status message', 'oso-super-cache'), 'error');
            }
        }
    }

    /**
     * collectURLs function.
     *
     * @access private
     * @param mixed $matches
     * @return void
     */
    private function collectURLs($matches)
    {
        $this->collectedURLs[$matches[2]] = $matches[2];
    }

    /**
     * handleIndexingProcess function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function handleIndexingProcess($data)
    {
        $returnData = [
            'success'=>false,
        ];

        if (!empty($data['url']) && filter_var($data['url'], FILTER_VALIDATE_URL)) {

            // Get all URLs and remove the URL which should be preloaded
            $xmlSitemapURLs = get_option('OSOSuperCacheXMLSitemapURLs', false);

            if (!empty($xmlSitemapURLs)) {

                if (!empty($xmlSitemapURLs['urls'][$data['url']])) {

                    $starttime = microtime(1);
                    $result = Factory::get('Cache\Frontend\InstantPreloader')->preload($_POST['url']);
                    $endtime = microtime(1);

                    if ($result) {

                        unset($xmlSitemapURLs['urls'][$data['url']]);

                        if (count($xmlSitemapURLs['urls'])) {
                            update_option('OSOSuperCacheXMLSitemapURLs', $xmlSitemapURLs, 'no');
                        } else {
                            delete_option('OSOSuperCacheXMLSitemapURLs');
                        }

                        $returnData = [
                            'ttcc'=>Factory::get('Cache\Tools')->floatRound($endtime-$starttime, 4),
                            'success'=>true,
                        ];
                    }
                }
            }
        }

        return $returnData;
    }
}
