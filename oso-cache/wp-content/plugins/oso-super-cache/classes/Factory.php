<?php
/*
 *
 *
 */

namespace OSOSuperCache;

class Factory
{

    private function __construct()
    {
    }

    public static function get($className)
    {
        $className = 'OSOSuperCache\\' . $className;

        if (class_exists($className)) {
            if (self::isSingleton($className)) {
                return $className::getInstance();
            } else {
                return new $className();
            }
        } else {
            return false;
        }
    }

    public static function isSingleton($className)
    {
        $reflection = new \ReflectionClass($className);

        if ($reflection->hasMethod('getInstance')) {
            return true;
        }

        return false;
    }
}
