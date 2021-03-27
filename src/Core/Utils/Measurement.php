<?php

namespace GWM\Core\Utils {
    /**
     * Class Measurement
     * @package GWM\Core\Utils
     * @version 1.0.0
     * @final
     */
    final class Measurement
    {
        static function Byte(float $bytes, int $times): float
        {
            return $times <= 0 ? $bytes : self::Byte($bytes / 1000.0, --$times);
        }
    }
}