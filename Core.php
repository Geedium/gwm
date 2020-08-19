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
            echo '@event-disabled<br/>';
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
            $namespaces = [__NAMESPACE__, 'Core'];
            $classNamespaces = explode('\\', $className);
            $namespace = array_diff($classNamespaces, $namespaces);
            $value = implode('\\', $namespace);
            $result = str_replace('\\', '/', $value);
            
            if(!@include_once("Core/$result.php"))
            {
                echo "Failed to include $result.php<br/>";
            }
            else
            {
                \class_alias("GWM\\Core\\$className", $className);
            }
        }
    }

    return new Core;
}