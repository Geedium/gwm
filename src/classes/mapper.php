<?php

class Mapper implements ISingleton
{
    private static array $class_map = [
        JWT::class => \Firebase\JWT\JWT::class
    ];

    private static ?Mapper $instance = null;

    public static function get_class_map(): array
    {
        return self::$class_map;
    }
    
    public static function get()
    {
        return !self::$instance ? (self::$instance = new static) : self::$instance;
    }
    
    private function __construct()
    {
        $this->init();
    }

    public function init()
    {
        
    }
}