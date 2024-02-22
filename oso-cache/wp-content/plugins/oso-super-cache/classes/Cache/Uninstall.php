<?php
/*
 *
 */

namespace OSOSuperCache\Cache;

use OSOSuperCache\Factory;

class Uninstall {

    private static $instance;

    public static function getInstance () {

        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __clone () {}

    private function __wakeup () {}

    public function __construct () {}

    public function uninstallPlugin () {

        global $wpdb;

        if (is_multisite()) {

            $allBlogs = $wpdb->get_results('
                SELECT
                    `blog_id`
                FROM
                    `'.$wpdb->prefix.'blogs`
            ');

            if (!empty($allBlogs)) {

                $originalBlogId = get_current_blog_id();

                foreach ($allBlogs as $blogData) {

                    $tableName = $wpdb->prefix.$blogData->blog_id.'_oso_super_cache_pages';

                    $wpdb->query("DROP TABLE IF EXISTS `".$tableName."`");

                    switch_to_blog($blogData->blog_id);

                    delete_option('OSOSuperCacheActivatedMessage');
                    delete_option('OSOSuperCacheActiveCacheTasks');
                    delete_option('OSOSuperCacheConfigActive');
                    delete_option('OSOSuperCacheConfigChanged');
                    delete_option('OSOSuperCacheConfigCustom');
                    delete_option('OSOSuperCacheConfigInactive');
                    delete_option('OSOSuperCacheConfigPreset');
                    delete_option('OSOSuperCacheLicenseData');
                    delete_option('OSOSuperCacheLicenseKey');
                    delete_option('OSOSuperCacheLicensePurchaseCode');
                    delete_option('OSOSuperCacheOptimizedDatabaseSavedBytes');
                    delete_option('OSOSuperCachePreloadedStats');
                    delete_option('OSOSuperCachePreloadedLastRequest');
                    delete_option('OSOSuperCacheSystemChangedMessage');
                    delete_option('OSOSuperCacheUpdateAvailableVersion');
                    delete_option('OSOSuperCacheUpdateLastCheck');
                    delete_option('OSOSuperCacheUnlinkData');
                    delete_option('OSOSuperCacheVersion');
                    delete_option('OSOSuperCacheXMLSitemapIndexFiles');
                    delete_option('OSOSuperCacheXMLSitemapURLs');

                    /* Deprecated */
                    delete_option('OSOSuperCacheInstallMessage');
                }

                switch_to_blog($originalBlogId);
            }

        } else {
            delete_option('OSOSuperCacheActivatedMessage');
            delete_option('OSOSuperCacheActiveCacheTasks');
            delete_option('OSOSuperCacheConfigActive');
            delete_option('OSOSuperCacheConfigChanged');
            delete_option('OSOSuperCacheConfigCustom');
            delete_option('OSOSuperCacheConfigInactive');
            delete_option('OSOSuperCacheConfigPreset');
            delete_option('OSOSuperCacheLicenseData');
            delete_option('OSOSuperCacheLicenseKey');
            delete_option('OSOSuperCacheLicensePurchaseCode');
            delete_option('OSOSuperCacheOptimizedDatabaseSavedBytes');
            delete_option('OSOSuperCachePreloadedStats');
            delete_option('OSOSuperCachePreloadedLastRequest');
            delete_option('OSOSuperCacheSystemChangedMessage');
            delete_option('OSOSuperCacheUpdateAvailableVersion');
            delete_option('OSOSuperCacheUpdateLastCheck');
            delete_option('OSOSuperCacheUnlinkData');
            delete_option('OSOSuperCacheVersion');
            delete_option('OSOSuperCacheXMLSitemapIndexFiles');
            delete_option('OSOSuperCacheXMLSitemapURLs');

            /* Deprecated */
            delete_option('OSOSuperCacheInstallMessage');
        }

        $tableName = $wpdb->prefix.'oso_super_cache_pages';

        $wpdb->query("DROP TABLE IF EXISTS `".$tableName."`");

        // Delete cache folder
        $cacheFolder = realpath(__DIR__.'/../../../../').'/cache/oso_super_cache';

        if (file_exists($cacheFolder)) {
            Factory::get('Cache\Frontend\Garbage')->deleteFilesInDirectory($cacheFolder, true, true, true);

            rmdir($cacheFolder);
        }
    }
}
?>