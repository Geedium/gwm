<?php

namespace GWM\Core\Utils {
    /**
     * Class Wrap
     * @package GWM\Core\Utils
     */
    class Wrap
    {
        public static function Reflection(callable $callable): void
        {
            try {
                $callable();
            } catch (\ReflectionException $exception) {
                // @TODO: Error handle.
            }
        }

        public static function isStringNotEmpty(callable $callable, string $string): void
        {
            !!empty($string) ?: $callable($string);
        }
    }
}