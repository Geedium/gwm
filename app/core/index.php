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
         * Undocumented function
         *
         * @return boolean
         */
        public static function isCGI()
        {
            return \preg_match('|cgi|fpm|', PHP_SAPI);
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

            !@include_once("app/core/src/$value.php");
            \class_alias("GWM\\Core\\$ivalue", $ivalue);
        }
    }

    chdir(__DIR__);

    if (is_dir('vendor') == false) {
        die('Missing dependencies (run composer update) in vendor dirs.');
    }

    $composer = json_decode(file_get_contents('composer.json'), true)['require'];
    
    require_once 'vendor/autoload.php';

    chdir(GWM['DIR_ROOT']);
    
    return new Core;
}