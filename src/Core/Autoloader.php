<?php

namespace GWM\Core;

/**
 * Autoloader
 * 
 * Custom autoloader. Might improve
 * performance if no third-party
 * dependencies used.
 * 
 * @version 1.0.0
 * @final
 */
final class Autoloader
{
    /**
     * Undocumented function
     *
     * Enable custom autoloading.
     *
     * @return boolean
     * @since 1.0.0
     * @final
     */
    final public static function Enable() : bool
    {
        return spl_autoload_register([__CLASS__, 'Load']);
    }

    /**
     * Undocumented function
     *
     * Disable custom autoloading.
     *
     * @return boolean
     * @since 1.0.0
     * @final
     */
    final public static function Disable() : bool
    {
        return spl_autoload_unregister([__CLASS__, 'Load']);
    }

    /**
     * Undocumented function
     *
     * @param string $classname
     * @return void
     * @since 1.0.0
     * @final
     */
    final private static function Load(string $classname) : void
    {
        $namespace = explode('\\', $classname);
        $namespaces = sizeof($namespace);

        if ($namespace[0] !== 'GWM') {
            return;
        }

        array_shift($namespace);

        $filename = implode('/', $namespace);
        $invert = str_replace('/', '\\', $filename);
        
        chdir(GWM['DIR_ROOT']);
        !include_once("src/$filename.php");
        class_alias("GWM\\$invert", $invert);
    }

    /**
     * Undocumented function
     * 
     * Exposes classes like global scope.
     *
     * @return void
     * @since 1.0.0
     * @final
     */
    final public static function Preload() : void
    {
        $prerequisites = [
            'Distributor' => Distributor::class,
            'Annotations' => Annotations::class,
            'Router' => Router::class,
            'Request' => Request::class
        ];

        foreach ($prerequisites as $key => $value) {
            class_exists($key) ?: class_alias($value, $key);
        }
    }
}
