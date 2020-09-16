<?php

/**
 * Undocumented interface
 */
interface ILoader
{
    public static function Enable();
    public static function Disable();
}

/**
 * Undocumented class
 * 
 * @version 1.0.0
 * @deprecated 1.0.0
 */
final class Autoloader implements ILoader
{
    /**
     * @magic
     */
    public function __construct()
    {
        self::Enable();
    }

    /**
     * Undocumented function
     *
     * @return void
     * @since 1.0.0
     */
    public static function Enable() : void
    {
        spl_autoload_register([__CLASS__, 'Load']);
    }

    /**
     * Undocumented function
     *
     * @return void
     * @since 1.0.0
     */
    public static function Disable() : void
    {
        spl_autoload_unregister([__CLASS__, 'Load']);
    }

    /**
     * Undocumented function
     *
     * @param string $classname
     * @return void
     * @since 1.0.0
     */
    private static function Load(string $classname) : void
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
}

return new Loader;