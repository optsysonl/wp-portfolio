<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Backend;

use OSOSuperCache\Factory;

class Fragments
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
        // Load inactive config
        Factory::get('Cache\Config')->loadConfig('inactive');
    }

    public function display()
    {
        $this->imagePath = plugins_url('images', realpath(__DIR__.'/../../'));

        if (!empty($_POST['formSend']) && check_admin_referer('oso_super_cache_fragments')) {
            $this->saveFragment($_POST);
        }

        $checkboxFragmentCaching            = Factory::get('Cache\Config')->get('fragmentCaching', 'inactive') ? ' checked' : '';
        $inputFragmentCachingMaskPhrase     = Factory::get('Cache\Config')->get('fragmentCachingMaskPhrase', 'inactive');

        if (!empty($inputFragmentCachingMaskPhrase)) {
            $inputFragmentCachingMaskPhrase = preg_replace('/[^a-zA-Z0-9]+/', '', $inputFragmentCachingMaskPhrase);
        }

        if (empty($inputFragmentCachingMaskPhrase) || strlen($inputFragmentCachingMaskPhrase) < 14) {
            $inputFragmentCachingMaskPhrase = Factory::get('Cache\Tools')->generateRandomString(16);
        }

        include Factory::get('Cache\Backend\Backend')->templatePath.'/fragments.html.php';
    }

    public function saveFragment($data)
    {
        $inactiveConfig = Factory::get('Cache\Config')->get(null, 'inactive');
        $inactiveConfig['fragmentCaching'] = !empty($data['fragmentCaching']) ? true : false;
        $inactiveConfig['fragmentCachingMaskPhrase'] = preg_replace('/[^a-zA-Z0-9]+/', '', $data['fragmentCachingMaskPhrase']);

        // Save config
        Factory::get('Cache\Config')->saveConfig($inactiveConfig, 'inactive');

        Factory::get('Cache\Config')->setCurrentPreset('custom');
    }
}
