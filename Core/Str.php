<?php

declare(strict_types=1);

namespace GWM\Core;

/**
 * Undocumented class
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
     * @return string
     */
    static function cat(string &$dest, string &$src) : string
    {
        return $dest.= $src;
    }

    /**
     * Join strings
     * 
     * Concatenates multiple strings.
     *
     * @param string $value
     * @return string
     */
    static function join(string $value) : string
    {
        $argruments = \func_get_args();
        return function () use (&$value) {
            for ($i = 0; $i < $argruments; $i++) {
                $argrument = &$argruments[$i];
                static::cat($value, $argrument);
            }
        };
    }
}