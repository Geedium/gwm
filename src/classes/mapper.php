<?php

/**
 * Mappper
 *
 * Leverages vendored and core class
 * access across global scope.
 *
 * @version 1.1.0
 */
class Mapper implements ISingleton
{
    /** @var array Contains aliases and classes. */
    private static array $class_map = [
        JWT::class => \Firebase\JWT\JWT::class
    ];

    /** @var Mapper|null Global instance of this object. */
    private static ?Mapper $instance = null;

    /**
     * Returns a copy of class map array.
     * @return array
     * @since 1.0.0
     */
    public static function get_class_map(): array
    {
        return self::$class_map;
    }
    
    /**
     * Get instance of an object.
     * @return Mapper
     * @since 1.0.0
     */
    public static function get(): Mapper
    {
        return !self::$instance ? (self::$instance = new static) : self::$instance;
    }
    
    /**
     * Initializes singleton instance.
     * @since 1.0.0
     */
    public function init()
    {
    }

    /** @magic */
    private function __construct()
    {
        $this->init();
    }
}
