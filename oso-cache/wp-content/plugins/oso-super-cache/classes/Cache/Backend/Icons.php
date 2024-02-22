<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Backend;

use OSOSuperCache\Factory;

class Icons {

    private static $instance;

    public static function getInstance () {

        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __clone () {}

    private function __wakeup () {}

    protected function __construct () {}

    public function getAdminSVGIcon () {

        return ' dashicons-admin-tools';

    }
}