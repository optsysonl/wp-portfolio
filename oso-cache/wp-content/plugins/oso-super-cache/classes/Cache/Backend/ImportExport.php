<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Backend;

use OSOSuperCache\Factory;

class ImportExport {

    private static $instance;

    private $imagePath;

    public static function getInstance () {

        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __clone () {}

    private function __wakeup () {}

    protected function __construct () {}

    public function display () {

        $this->imagePath = plugins_url('images', realpath(__DIR__.'/../../'));

        if (!empty($_POST['formSend']) && !empty($_POST['importSettings']) && check_admin_referer('oso_super_cache_import_export')) {
            $this->importSettings($_POST['importSettings']);
        }

        $textareaExportSettings = esc_textarea(json_encode(Factory::get('Cache\Config')->get(null, 'active')));

        include Factory::get('Cache\Backend\Backend')->templatePath.'/import-export.html.php';

    }

    public function importSettings ($settings) {

        $settings = stripslashes($settings);

        // Check if settings is json
        if (!Factory::get('Cache\Tools')->isStringJSON($settings)) {

            Factory::get('Cache\Backend\Backend')->addMessage(_x('Your settings are not valid JSON.', 'Status message', 'oso-super-cache'), 'error');

        } else {

            $data = json_decode($settings, true);

            if (!empty($data['cacheLifetime'])) {
                $data['cacheLifetimeHome']            = !empty($data['cacheLifetime']['home']) ? intval($data['cacheLifetime']['home']) : 0;
                $data['cacheLifetimeArchives']['-']   = !empty($data['cacheLifetime']['archives']['-']) ? intval($data['cacheLifetime']['archives']['-']) : 0;
                $data['cacheLifetimePostType']['-']   = !empty($data['cacheLifetime']['postType']['-']) ? intval($data['cacheLifetime']['postType']['-']) : 0;
                $data['cacheLifetimeFeed']            = !empty($data['cacheLifetime']['feed']) ? intval($data['cacheLifetime']['feed']) : 0;
                $data['cacheLifetime404']             = !empty($data['cacheLifetime']['404']) ? intval($data['cacheLifetime']['404']) : 604800;
                $data['cacheLifetimeGarbage']         = !empty($data['cacheLifetime']['garbage']) ? intval($data['cacheLifetime']['garbage']) : 31536000;

                // Check post types
                $allPostTypes = Factory::get('Cache\Backend\AdvancedSettings')->getPostTypes();

                if (!empty($data['cacheLifetime']['archives'])) {
                    foreach ($data['cacheLifetime']['archives'] as $key => $value) {

                        // Check if post type exists
                        if ($key != '-' && !empty($allPostTypes[$key])) {
                            $data['cacheLifetimeArchives'][$key]  = !empty($value) ? intval($value) : 0;
                        }
                    }
                }

                if (!empty($data['cacheLifetime']['postType'])) {
                    foreach ($data['cacheLifetime']['postType'] as $key => $value) {

                        // Check if post type exists
                        if ($key != '-' && !empty($allPostTypes[$key])) {
                            $data['cacheLifetimePostType'][$key]  = !empty($value) ? intval($value) : 0;
                        }
                    }
                }

                unset($data['cacheLifetime']);
            }

            if (!empty($data['cacheDontCachePagesContainQuery'])) {
                $data['cacheDontCachePagesContainQuery'] = addslashes(json_encode($data['cacheDontCachePagesContainQuery']));
            }

            if (!empty($data['cacheDontCachePagesContainPath'])) {
                $data['cacheDontCachePagesContainPath'] = implode("\n", $data['cacheDontCachePagesContainPath']);
            }

            if (!empty($data['cacheDontUseCacheWhenUserAgent'])) {
                $data['cacheDontUseCacheWhenUserAgent'] = implode("\n", $data['cacheDontUseCacheWhenUserAgent']);
            }

            if (!empty($data['cacheDontUseCacheWhenCookie'])) {
                $data['cacheDontUseCacheWhenCookie'] = implode("\n", $data['cacheDontUseCacheWhenCookie']);
            }

            if (!empty($data['cacheDontCachePagesOfPostType'])) {
                foreach ($data['cacheDontCachePagesOfPostType'] as $key => $postType) {
                    $data['cacheDontCachePagesOfPostType'][$postType] = $postType;
                    unset($data['cacheDontCachePagesOfPostType'][$key]);
                }
            }

            if (!empty($data['cacheDontCachePagesOfTaxonomy'])) {
                foreach ($data['cacheDontCachePagesOfTaxonomy'] as $key => $taxonomy) {
                    $data['cacheDontCachePagesOfTaxonomy'][$taxonomy] = $taxonomy;
                    unset($data['cacheDontCachePagesOfTaxonomy'][$key]);
                }
            }

            if (!empty($data['browserSecurityContentSecurityPolicy'])) {
                $data['browserSecurityContentSecurityPolicy'] = implode("\n", $data['browserSecurityContentSecurityPolicy']);
            }

            Factory::get('Cache\Backend\AdvancedSettings')->saveGeneral($data);
            Factory::get('Cache\Backend\AdvancedSettings')->saveHTML($data);
            Factory::get('Cache\Backend\AdvancedSettings')->saveImage($data);
            Factory::get('Cache\Backend\AdvancedSettings')->saveJavaScript($data);
            Factory::get('Cache\Backend\AdvancedSettings')->saveCSS($data);
            Factory::get('Cache\Backend\AdvancedSettings')->saveCacheLifetimes($data);
            Factory::get('Cache\Backend\AdvancedSettings')->saveCacheExceptions($data);
            Factory::get('Cache\Backend\AdvancedSettings')->saveBrowser($data);
            Factory::get('Cache\Backend\AdvancedSettings')->saveMiscellaneous($data);
            Factory::get('Cache\Backend\AdvancedSettings')->saveDebug($data);

            Factory::get('Cache\Backend\CDNSettings')->saveCDN($data);
            Factory::get('Cache\Backend\Fragments')->saveFragment($data);

            Factory::get('Cache\Backend\Backend')->addMessage(_x('Import successfully.', 'Status message', 'oso-super-cache'), 'success');
        }
    }
}
?>