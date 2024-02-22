<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class Preload
{

    private static $instance;
    private $resources = [];

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
     * add function.
     *
     * @access public
     * @param mixed $url
     * @param mixed $type
     * @return void
     */
    public function add($url, $type)
    {
        if (empty($this->resources[$url])) {

            if (in_array($type, ['script', 'style'])) {
                $this->resources[$url] = [
                    'url' => $url,
                    'type' => $type,
                    'issued' => false,
                ];
            }
        }
    }

    /**
     * printPreloadHTML function.
     *
     * @access public
     * @return void
     */
    public function printPreloadHTML()
    {
        echo $this->getPreloadHTML();
    }

    /**
     * getPreloadHTML function.
     *
     * @access public
     * @return void
     */
    public function getPreloadHTML()
    {
        $html = '';

        if (!empty($this->resources)) {
            foreach ($this->resources as $resourceData) {
                if ($resourceData['issued'] == false) {
                    $html .= "<link rel=\"preload\" href=\"".$resourceData['url']."\" as=\"".$resourceData['type']."\">\n";

                    $this->resources[$resourceData['url']]['issued'] = true;
                }
            }
        }

        return $html;
    }
}
