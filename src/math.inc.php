<?php

/**
 * Undocumented function.
 * @param int|float $x Part. 
 * @param int|float $y Whole.
 * @return int
 */
function percent($x, $y = 100): int
{
    return (int)($x * 100 / $y);
}