<?php

declare(strict_types = 0);

namespace GWM\Core;

/**
 * Undocumented class
 */
abstract class Controller implements \JsonSerializable {
    /**
     * @magic
     */
    function __set(string $name, $value) {
        if($name == 'model') {
            if(is_object($value) and $value instanceof Model) {
                $this->model = $value;
            } else if(is_string($value) and $value != null) {
                $this->model = new $value;
            }
        }
    }
    
    /**
     * @see JsonSerializable::jsonSerialize
     */
    public function jsonSerialize() : mixed {
        return \get_object_vars($this);
    }
}