<?php

/**
 * Crypt
 * 
 * Referable array.
 * 
 * @version 1.1.0
 * @abstract
 */
abstract class Crypt
{
    /** @magic */
    public abstract function __construct();

    /**
     * Converts to array.
     * @return array
     * @since 1.1.0
     */
    public function to_array(): array
    {
        return get_object_vars($this);
    }
}