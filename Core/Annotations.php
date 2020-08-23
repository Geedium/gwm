<?php

namespace GWM\Core;

/**
 * Annotations
 */
class Annotations
{
    /**
     * @magic
     */
    function __construct($classname, &$data = null)
    {
        $data = \get_class_vars($classname);
        $class = new \ReflectionClass($classname);
         
        foreach ($data as $key => &$value) {
            $property = $class->getProperty($key)->getDocComment();
            \preg_match_all('#@(.*?)\n#s', $property, $matches, PREG_PATTERN_ORDER);

            if (substr($matches[0][0], 0, 4) === '@var') {
                $match = substr($matches[0][0], 5);
                $match = str_replace(')', '', $match);

                $value = explode('(', $match);
            }
        }
    }
}