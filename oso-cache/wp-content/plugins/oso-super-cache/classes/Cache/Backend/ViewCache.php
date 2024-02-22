<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Backend;

use OSOSuperCache\Factory;

class ViewCache
{
    private static $instance;

    private $imagePath;

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

    /**
     * display function.
     *
     * @access public
     * @return void
     */
    public function display()
    {
        $this->imagePath = plugins_url('images', realpath(__DIR__.'/../../'));

        $tab = !empty($_GET['tab']) ? $_GET['tab'] : 'pages';

        include Factory::get('Cache\Backend\Backend')->templatePath.'/view-cache.html.php';

        if ($tab == 'pages') {
            $this->displayPages();
        } elseif ($tab == '404') {
            $this->display404();
        } else {
            $this->displayPages();
        }
    }

    /**
     * displayPages function.
     *
     * @access public
     * @return void
     */
    public function displayPages()
    {
        global $wpdb;

        $searchString = '';

        if (!empty($_POST['formSend']) && !empty($_POST['cachedPageAction']) && !empty($_POST['urls']) && check_admin_referer('oso_super_cache_view_cache_action')) {
            if ($_POST['cachedPageAction'] == 'swap-dont-cache') {
                $this->swapDontCacheStatus($_POST['urls']);
            }

            if ($_POST['cachedPageAction'] == 'remove-from-cache') {
                $this->removeFromCache($_POST['urls']);
            }

            if ($_POST['cachedPageAction'] == 'mark-for-refresh') {
                $this->markForRefresh($_POST['urls']);
            }
        }

        // Search pages
        if (!empty($_POST['searchFormSend']) && check_admin_referer('oso_super_cache_view_cache_search')) {
            if (!empty($_POST['search-page'])) {
                $searchString = $this->prepareSearchString($_POST['search-page']);
                $pagesData = $this->getPages('`is_404`=0 AND `url` LIKE "'.$wpdb->_escape($searchString).'%" ');
            } else {
                // User removed search string to reset search results
                $pagesData = $this->getPages();
            }
        } else {
            // User searched before and used paging
            if (!empty($_GET['search-page'])) {
                $searchString = $this->prepareSearchString($_GET['search-page']);
                $pagesData = $this->getPages('`is_404`=0 AND `url` LIKE "'.$wpdb->_escape($searchString).'%" ');
            } else {
                $pagesData = $this->getPages();
            }
        }

        $currentPage = !empty($_GET['paged']) ? intval($_GET['paged']) : 1;

        if ($currentPage > $pagesData['totalPages']) {
            $currentPage = 1;
        }

        include Factory::get('Cache\Backend\Backend')->templatePath.'/view-cache-pages.html.php';
    }

    /**
     * display404 function.
     *
     * @access public
     * @return void
     */
    public function display404()
    {
        global $wpdb;

        $searchString = '';

        if (!empty($_POST['formSend']) && !empty($_POST['cachedPageAction']) && !empty($_POST['urls']) && check_admin_referer('oso_super_cache_view_cache_action_404')) {
            if ($_POST['cachedPageAction'] == 'remove-from-cache') {
                $this->removeFromCache($_POST['urls']);
            }
        }

        // Search pages
        if (!empty($_POST['searchFormSend']) && check_admin_referer('oso_super_cache_view_cache_search_404')) {
            if (!empty($_POST['search-page'])) {
                $searchString = $this->prepareSearchString($_POST['search-page']);
                $pagesData = $this->getPages('`is_404`=1 AND `url` LIKE "'.$wpdb->_escape($searchString).'%" ');
            } else {
                // User removed search string to reset search results
                $pagesData = $this->getPages('`is_404`=1');
            }
        } else {
            // User searched before and used paging
            if (!empty($_GET['search-page'])) {
                $searchString = $this->prepareSearchString($_GET['search-page']);
                $pagesData = $this->getPages('`is_404`=1 AND `url` LIKE "'.$wpdb->_escape($searchString).'%" ');
            } else {
                $pagesData = $this->getPages('`is_404`=1');
            }
        }

        $currentPage = !empty($_GET['paged']) ? intval($_GET['paged']) : 1;

        if ($currentPage > $pagesData['totalPages']) {
            $currentPage = 1;
        }

        include Factory::get('Cache\Backend\Backend')->templatePath.'/view-cache-404.html.php';
    }

    /**
     * getPages function.
     *
     * @access public
     * @param string $where (default: '`is_404`=0')
     * @return void
     */
    public function getPages($where = '`is_404`=0')
    {
        global $wpdb;

        $itemsPerPage = 20;

        $totalPages = $wpdb->get_results('
            SELECT
                COUNT(*) as `total`
            FROM
                `'.$wpdb->prefix.'oso_super_cache_pages`
            WHERE
                '.$where.'
        ');

        $paged = !empty($_GET['paged']) ? intval($_GET['paged']) : 1;

        if ($paged > ceil($totalPages[0]->total/$itemsPerPage)) {
            $paged = 1;
        }

        $pages = $wpdb->get_results('
            SELECT
                `domain`,
                `hash`,
                `https`,
                `url`,
                `dont_cache`,
                `runtime_without_cache`,
                `runtime_with_cache`,
                `last_updated`,
                `next_update`
            FROM
                `'.$wpdb->prefix.'oso_super_cache_pages`
            WHERE
                '.$where.'
            ORDER BY
                `url` ASC
            LIMIT '.intval(($paged*$itemsPerPage)-$itemsPerPage).', '.$itemsPerPage.'
        ');

        return !empty($totalPages) ? ['pages'=>$pages, 'totalPages'=>ceil($totalPages[0]->total/$itemsPerPage), 'totalItems'=>$totalPages[0]->total] : false;
    }

    /**
     * swapDontCacheStatus function.
     *
     * @access public
     * @param mixed $urls
     * @return void
     */
    public function swapDontCacheStatus($urls)
    {
        global $wpdb;

        if (is_array($urls)) {
            foreach ($urls as $hash) {
                $https = strpos($hash, 'https-') !== false ? '1' : '0';
                $hash = str_replace('https-', '', $hash);

                $wpdb->query('
                    UPDATE
                        `'.$wpdb->prefix.'oso_super_cache_pages`
                    SET
                        `dont_cache`=IF(`dont_cache`, 0, 1)
                    WHERE
                        `hash`="'.$wpdb->_escape($hash).'"
                        AND
                        `https`="'.$https.'"
                ');
            }
        }

        Factory::get('Cache\Backend\Backend')->addMessage(_x("&quot;Cache storage&quot; status successfully swapped for selected paths.", 'Status message', 'oso-super-cache'), 'success');
    }

    /**
     * removeFromCache function.
     *
     * @access public
     * @param mixed $urls
     * @return void
     */
    public function removeFromCache($urls)
    {
        global $wpdb;

        if (is_array($urls)) {
            foreach ($urls as $hash) {
                $https = strpos($hash, 'https-') !== false ? '1' : '0';
                $hash = str_replace('https-', '', $hash);

                $wpdb->query('
                    DELETE FROM
                        `'.$wpdb->prefix.'oso_super_cache_pages`
                    WHERE
                        `hash`="'.$wpdb->_escape($hash).'"
                        AND
                        `https`="'.$https.'"
                ');
            }
        }

        Factory::get('Cache\Backend\Backend')->addMessage(_x("Selected paths successfully removed from cache.", 'Status message', 'oso-super-cache'), 'success');
    }

    /**
     * markForRefresh function.
     *
     * @access public
     * @param mixed $urls
     * @return void
     */
    public function markForRefresh($urls)
    {
        global $wpdb;

        if (is_array($urls)) {
            foreach ($urls as $hash) {
                $https = strpos($hash, 'https-') !== false ? '1' : '0';
                $hash = str_replace('https-', '', $hash);

                $wpdb->query('
                    UPDATE
                        `'.$wpdb->prefix.'oso_super_cache_pages`
                    SET
                        `last_updated`="0000-00-00 00:00:00",
                        `next_update`="0000-00-00 00:00:00",
                        `runtime_with_cache`=0
                    WHERE
                        `hash`="'.$wpdb->_escape($hash).'"
                        AND
                        `https`="'.$https.'"
                ');
            }
        }

        Factory::get('Cache\Backend\Backend')->addMessage(_x("Selected paths successfully marked for cache refresh.", 'Status message', 'oso-super-cache'), 'success');
    }

    /**
     * prepareSearchString function.
     *
     * @access public
     * @param mixed $searchString
     * @return void
     */
    public function prepareSearchString($searchString)
    {
        $searchString = sanitize_text_field($searchString);

        // When / is not the first char we make an imprecise search because that is what the user expects
        if (substr($searchString, 0, 1) != '/') {
            $searchString = '%'.$searchString;
        }

        return $searchString;
    }
}
