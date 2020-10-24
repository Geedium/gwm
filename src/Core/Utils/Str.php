<?php

declare(strict_types=1);
namespace GWM\Core\Utils;

/**
 * Undocumented class
 * 
 * @version 1.0.0
 */
class Str
{
    /**
     * Concatenate strings
     * 
     * Concatenates two strings together.
     *
     * @param string $dest
     * @param string $src
     * @return string|null
     * @since 1.0.0
     */
    static function cat(string &$dest, string &$src) :? string
    {
        return $dest.= $src;
    }

    /**
     * Join strings
     * 
     * Concatenates multiple strings.
     *
     * @param string $value
     * @return \Closure
     * @since 1.0.0
     */
    static function join(string $value) : \Closure
    {
        $arguments = \func_get_args();
        return function () use ($arguments, &$value) {
            for ($i = 0; $i < $arguments; $i++) {
                $argument = &$arguments[$i];
                static::cat($value, $argument);
            }
        };
    }
}