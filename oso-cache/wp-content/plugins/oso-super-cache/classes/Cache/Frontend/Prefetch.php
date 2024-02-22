<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class Prefetch
{

    private static $instance;
    private $hosts = [];

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
     * addHost function.
     *
     * @access public
     * @param mixed $hostname
     * @return void
     */
    public function addHost($hostname)
    {
        if (empty($this->hosts[$hostname])) {
            $this->hosts[$hostname] = [
                'host'=>$hostname,
                'issued'=>false,
            ];
        }
    }

    /**
     * printDNSPrefetchHTML function.
     *
     * @access public
     * @return void
     */
    public function printDNSPrefetchHTML()
    {
        echo $this->getDNSPrefetchHTML();
    }

    /**
     * getDNSPrefetchHTML function.
     *
     * @access public
     * @return void
     */
    public function getDNSPrefetchHTML()
    {
        $html = '';

        if (!empty($this->hosts)) {
            foreach ($this->hosts as $hostData) {
                if ($hostData['issued'] == false) {
                    $html .= "<link rel=\"dns-prefetch\" href=\"//".$hostData['host']."\">\n";

                    $this->hosts[$hostData['host']]['issued'] = true;
                }
            }
        }

        return $html;
    }
}
