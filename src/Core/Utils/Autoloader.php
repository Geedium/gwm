<?php

namespace GWM\Core\Utils {
    /**
     * Class Autoloader
     *
     * Custom autoloader designed to resolve
     * paths in src folder. Might improve
     * performance if no third-party
     * dependencies required.
     *
     * @package GWM\Core\Utils
     * @version 1.0.0
     * @final
     */
    final class Autoloader
    {
        /**
         * Function Enable
         *
         * Enable auto-loader.
         *
         * @return bool
         * @since 1.0.0
         * @final
         */
        final static function Enable(): bool
        {
            return spl_autoload_register([__CLASS__, 'Load']);
        }

        /**
         * Function Disable
         *
         * Disable auto-loader.
         *
         * @return bool
         * @since 1.0.0
         * @final
         */
        final static function Disable(): bool
        {
            return spl_autoload_unregister([__CLASS__, 'Load']);
        }

        /**
         * Function Load
         *
         * No description.
         *
         * @param string $classname
         * @return void
         * @since 1.0.0
         * @final
         */
        final private static function Load(string $classname): void
        {
            $namespace = explode('\\', $classname);

            if ($namespace[0] !== 'GWM') {
                return;
            }

            array_shift($namespace);

            $filename = implode('/', $namespace);

            chdir(GWM['DIR_ROOT']);
            !include_once("src/$filename.php");
        }
    }
}
