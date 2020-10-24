<?php

declare(strict_types=1);
namespace GWM\Core\Utils;

/**
 * Undocumented class
 * 
 * @version 1.0.0
 * @category GWM
 * @package Core\Utils
 */
class Debug
{
    private static $trace = [];

    public static $log = [];

    static function Register() : void
    {
        self::$trace = array_merge(debug_backtrace(), self::$trace);
    }

    static function Get() : void
    {
        self::Dump(self::$trace);
    }

    /**
     * Dump
     *
     * @param mixed ...$params
     * @return void
     * @since 1.0.0
     */
    static function Dump(...$params) : void
    {
        echo '<pre>';
        foreach ($params as $key => $value) {
            if (isset($value) & is_array($value) & $key != null) {
                self::Dump($value);
                continue;
            }
            var_dump($key, $value);
        }
        echo '</pre>';
    }
}