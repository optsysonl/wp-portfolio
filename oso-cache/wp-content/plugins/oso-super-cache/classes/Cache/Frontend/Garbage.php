<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class Garbage
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

    public function __construct()
    {
    }

    /**
     * clearCache function.
     *
     * @access public
     * @return void
     */
    public function clearCache()
    {
        global $wpdb;

        // mark all cached pages as outdated
        $wpdb->query('
            UPDATE
                `'.$wpdb->prefix.'oso_super_cache_pages`
            SET
                `last_updated`="0000-00-00 00:00:00",
                `next_update`="0000-00-00 00:00:00",
                `runtime_with_cache`=0
            WHERE
                `is_404`=0
        ');

        // Remove old 404 entries
        $this->remove404Entries();

        // Remove css files
        $this->clearStylesCache();

        // Remove js files
        $this->clearScriptsCache();
    }

    /**
     * clearScriptsCache function.
     *
     * @access public
     * @return void
     */
    public function clearScriptsCache()
    {
        $pathToScripts = Factory::get('Cache\Frontend\Cache')->getMainCacheFolderPath().Factory::get('Cache\Frontend\Cache')->getFolderByCacheType('js');

        $this->deleteFilesInDirectory($pathToScripts, true);
    }

    /**
     * clearStylesCache function.
     *
     * @access public
     * @return void
     */
    public function clearStylesCache()
    {
        $pathToStyles = Factory::get('Cache\Frontend\Cache')->getMainCacheFolderPath().Factory::get('Cache\Frontend\Cache')->getFolderByCacheType('css');

        $this->deleteFilesInDirectory($pathToStyles, true);
    }

    /**
     * clearPageCache function.
     *
     * @access public
     * @return void
     */
    public function clearPageCache()
    {
        $pathToPages = Factory::get('Cache\Frontend\Cache')->getMainCacheFolderPath().Factory::get('Cache\Frontend\Cache')->getFolderByCacheType('page');

        $this->deleteFilesInDirectory($pathToPages);
    }

    /**
     * clearPreCacheFiles function.
     *
     * @access public
     * @return void
     */
    public function clearStylesPreCacheFiles()
    {
        $pathToStyles = Factory::get('Cache\Frontend\Cache')->getMainCacheFolderPath().Factory::get('Cache\Frontend\Cache')->getFolderByCacheType('css');

        if (file_exists($pathToStyles)) {
            foreach (new \DirectoryIterator($pathToStyles) as $fileInfo) {
                // Ignore . and ..
                if (!$fileInfo->isDot()) {
                    if ($fileInfo->isFile()) {
                        if (strpos($fileInfo->getFilename(), 'pre_cache_') !== false) {
                            unlink($fileInfo->getPathname());
                        }
                    }
                }
            }
        }
    }

    /**
     * deleteFilesInDirectory function.
     *
     * @access public
     * @param mixed $dir
     * @param bool $ignoreMaxLifetime (default: false)
     * @param bool $deleteFolder (default: false)
     * @param bool $ignoreBlacklist (default: false)
     * @return void
     */
    public function deleteFilesInDirectory($dir, $ignoreMaxLifetime = false, $deleteFolder = false, $ignoreBlacklist = false)
    {
        $maxLifetime = Factory::get('Cache\Config')->get('cacheLifetime')['garbage'];

        if (file_exists($dir)) {
            foreach (new \DirectoryIterator($dir) as $fileInfo) {
                // Ignore . and ..
                if (!$fileInfo->isDot()) {
                    // If folder, delete files in folder
                    if ($fileInfo->isDir()) {
                        // We don't delete folders
                        $this->deleteFilesInDirectory($fileInfo->getPathname(), $ignoreMaxLifetime, $deleteFolder, $ignoreBlacklist);

                        if ($deleteFolder) {
                            if ($this->isDirectoryEmpty($fileInfo->getPathname())) {
                                rmdir($fileInfo->getPathname());
                            }
                        }
                    } else {
                        if (!in_array($fileInfo->getFilename(), ['.htaccess']) || $ignoreBlacklist) {
                            // Check lifetime
                            if ($ignoreMaxLifetime || (!$ignoreMaxLifetime && ($fileInfo->getMTime()+$maxLifetime) < time())) {
                                // Delete file
                                unlink($fileInfo->getPathname());
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * isDirectoryEmpty function.
     *
     * @access public
     * @param mixed $dir
     * @return void
     */
    public function isDirectoryEmpty($dir)
    {
        $isEmpty = true;

        if (file_exists($dir)) {
            foreach (new \DirectoryIterator($dir) as $fileInfo) {
                if (!$fileInfo->isDot()) {
                    $isEmpty = false;
                }
            }
        } else {
            // In case something bad happens
            $isEmpty = false;
        }

        return $isEmpty;
    }

    /**
     * refreshCache function.
     *
     * @access public
     * @param int $postId (default: 0)
     * @param string $postType (default: '')
     * @param int $home (default: 0)
     * @param int $feed (default: 0)
     * @return void
     */
    public function refreshCache($postId = 0, $postType = '', $home = 0, $feed = 0)
    {
        global $wpdb;

        $statement = '
            UPDATE
                `'.$wpdb->prefix.'oso_super_cache_pages`
            SET
                `last_updated`="0000-00-00 00:00:00",
                `next_update`="0000-00-00 00:00:00",
                `runtime_with_cache`=0
            WHERE
                `is_404`=0
        ';

        if (!empty($postId)) {
            $statement .= ' AND `post_id`='.intval($postId);
        }

        if (!empty($postType)) {
            $statement .= ' AND `is_archive`=1 AND `post_type`="'.$wpdb->_escape($postType).'"';
        }

        if (!empty($home)) {
            $statement .= ' AND `is_home`=1';
        }

        if (!empty($feed)) {
            $statement .= ' AND `is_feed`=1';
        }

        $wpdb->query($statement);
    }

    /**
     * refreshCacheOfURL function.
     *
     * @access public
     * @param mixed $url
     * @param mixed $prefix
     * @return void
     */
    public function refreshCacheOfURL($url, $prefix)
    {
        global $wpdb;

        $urlInfo = parse_url($url);
        $isHttps = $urlInfo['scheme'] == 'https' ? 1 : 0;

        if (empty($urlInfo['path'])) {
            $urlInfo['path'] = '/';
        }

        $wpdb->query('
            UPDATE
                `'.$wpdb->prefix.'oso_super_cache_pages`
            SET
                `last_updated`="0000-00-00 00:00:00",
                `next_update`="0000-00-00 00:00:00",
                `runtime_with_cache`=0
            WHERE
                `domain`="'.$wpdb->_escape($urlInfo['host']).'"
                AND
                `https`="'.intval($isHttps).'"
                AND
                `prefix`="'.$wpdb->_escape($prefix).'"
                AND
                `url`="'.$wpdb->_escape($urlInfo['path']).'"
        ');
    }

    /**
     * removeFromCache function.
     *
     * @access public
     * @param mixed $postId
     * @return void
     */
    public function removeFromCache($postId)
    {
        global $wpdb;

        $wpdb->query('
            DELETE FROM
                `'.$wpdb->prefix.'oso_super_cache_pages`
            WHERE
                `post_id`="'.$wpdb->_escape($postId).'"
        ');
    }

    /**
     * remove404FromCache function.
     *
     * @access public
     * @param mixed $url
     * @return void
     */
    public function remove404FromCache($url)
    {
        global $wpdb;

        $wpdb->query('
            DELETE FROM
                `'.$wpdb->prefix.'oso_super_cache_pages`
            WHERE
                `url`="'.$wpdb->_escape($url).'"
                AND
                `is_404`=1
        ');
    }

    /**
     * remove404Entries function.
     *
     * @access public
     * @return void
     */
    public function remove404Entries()
    {
        global $wpdb;

        $wpdb->query('
            DELETE FROM
                `'.$wpdb->prefix.'oso_super_cache_pages`
            WHERE
                `is_404`=1
                AND
                (UNIX_TIMESTAMP(`last_updated`)+'.intval(Factory::get('Cache\Config')->get('cacheLifetime')['404']).') < UNIX_TIMESTAMP()
        ');
    }
}
