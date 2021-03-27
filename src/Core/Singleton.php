<?php

namespace GWM\Core {
    /**
     * Class Singleton
     * @internal
     * @package GWM\Core
     * @version 1.0.0
     */
    abstract class Singleton
    {
        abstract protected function Init(): void;

        /** @var array $instance */
        private static array $instance = [];

        /**
         * Singleton constructor.
         * @magic
         */
        private function __construct()
        {
            $this->Init();
        }

        /**
         * Function Get
         *
         * Get instance or instantiate.
         *
         * @return static|object
         * @since 1.0.0
         */
        static final function Get(): object
        {
            return !self::$instance[static::class] ? (self::$instance[static::class] = new static) : self::$instance[static::class];
        }
    }
}