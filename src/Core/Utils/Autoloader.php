<?php

namespace GWM\Core\Utils {
    /**
     * Class Autoloader
     *
     * No description.
     *
     * @internal
     * @package GWM\Core\Utils
     * @version 1.0.0
     * @final
     */
    abstract class Autoloader
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
         * @noinspection PhpExpressionResultUnusedInspection
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

            !include_once "src/$filename.php";
        }
    }
}
