<?php

namespace GWM
{
    /**
     * Core
     */
    class Core
    {
        /**
         * @magic
         */
        public function __construct()
        {
            self::enable();
        }

        /**
         * Enable Autoloading
         *
         * @return void
         */
        public static function enable()
        {
            $namespaces = [__NAMESPACE__, 'Core'];
            $namespace = implode('\\', $namespaces);
            $target = "$namespace::_autoload";
            spl_autoload_register($target);
        }

        /**
         * Disable Autoloading
         *
         * @return void
         */
        public static function disable()
        {
            spl_autoload_unregister('Core::_autoload');
        }

        /**
         * Autoloader
         *
         * @param string $className
         * @return void
         */
        private static function _autoload($className)
        {
            $include = explode('\\', $className);
            $exclude = [ __NAMESPACE__, 'Core'];
            $diff = array_diff($include, $exclude);
            $value = implode('/', $diff);
            $ivalue = str_replace('/', '\\', $value);

            !@include_once("Core/$value.php");
            \class_alias("GWM\\Core\\$ivalue", $ivalue);
        }
    }

    return new Core;
}