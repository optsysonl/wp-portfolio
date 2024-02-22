<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Backend;

use OSOSuperCache\Factory;

class CDNSettings
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

    /**
     * display function.
     *
     * @access public
     * @return void
     */
    public function display()
    {
        $this->imagePath = plugins_url('images', realpath(__DIR__.'/../../'));

        if (!empty($_POST['formSend']) && check_admin_referer('oso_super_cache_cdn')) {
            $this->saveCDN($_POST);
        }

        $checkboxCDN                    = Factory::get('Cache\Config')->get('cdn', 'inactive') ? ' checked' : '';
        $optionCDNProviderOther         = Factory::get('Cache\Config')->get('cdnProvider', 'inactive') === 'CDNOther' ? ' selected' : '';
        $optionCDNProviderStackPath     = Factory::get('Cache\Config')->get('cdnProvider', 'inactive') === 'CDNStackPath' ? ' selected' : '';
        $inputCDNURL                    = sanitize_text_field(Factory::get('Cache\Config')->get('cdnURL', 'inactive'));

        include Factory::get('Cache\Backend\Backend')->templatePath.'/cdn-settings.html.php';
    }

    /**
     * saveCDN function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function saveCDN($data)
    {

        $inactiveConfig = Factory::get('Cache\Config')->get(null, 'inactive');

        $inactiveConfig['cdn'] = !empty($data['cdn']) ? true : false;

        $inactiveConfig['cdnProvider']    = 'CDNOther';

        if (!empty($data['cdnProvider'])) {
            if ($data['cdnProvider'] == 'CDNStackPath') {
                $inactiveConfig['cdnProvider'] = 'CDNStackPath';
            }
        }

        $inactiveConfig['cdnURL'] = '';

        // If URL has no scheme, we add a pseudo scheme to get the real URL from parse_url
        if (strpos(substr($data['cdnURL'], 0, 8), '//') === false) {
            $data['cdnURL'] = 'https://'.$data['cdnURL'];
        }

        $cdnURLInfo = parse_url($data['cdnURL']);

        if (!empty($cdnURLInfo['host'])) {
            $inactiveConfig['cdnURL'] = $cdnURLInfo['host'];
        }

        // Save config
        Factory::get('Cache\Config')->saveConfig($inactiveConfig, 'inactive');

        Factory::get('Cache\Config')->setCurrentPreset('custom');
    }
}
