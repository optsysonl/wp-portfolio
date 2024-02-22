<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Backend;

use OSOSuperCache\Factory;

class View {

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

    protected function __construct () {

        $this->imagePath = plugins_url('images', realpath(__DIR__.'/../../'));

        // Detect apply or reset of config
        if (!empty($_POST['applyChanges'])) {

            $refreshCache = !empty($_POST['cacheMaintenanceRefresh']) ? true : false;

            Factory::get('Cache\Backend\AdvancedSettings')->applyNewConfig($refreshCache);
        }

        if (!empty($_POST['resetInactiveConfig'])) {
            Factory::get('Cache\Backend\AdvancedSettings')->resetNewConfig();
        }
    }

    public function __call($class, $args) {

        if (strpos($class, 'display__') !== false) {

            $this->displayHeader();

            $module = substr($class, strpos($class, '__')+2);

            Factory::get('Cache\Backend\\'.$module)->display();

            $this->displayFooter();
        }
    }

    public function displayHeader () {
        include Factory::get('Cache\Backend\Backend')->templatePath.'/header.html.php';
    }

    public function displayFooter () {
        include Factory::get('Cache\Backend\Backend')->templatePath.'/footer.html.php';
    }
}
?>