<?php

namespace GWM {

    use GWM\Core\Singleton;

    class Mapper extends Singleton
    {
        private static array $class_map = [
            JWT::class => \Firebase\JWT\JWT::class
        ];

        function __get($propertyName)
        {
            return self::$class_map[$propertyName];
        }

        public static function get_class_map(): array
        {
            return self::$class_map;
        }

        /**
         * @todo [Deprecated] Naming Convention
         */
        protected final function Init(): void
        {
            foreach (self::$class_map as $k => $v) {
                class_exists($k) && class_alias($k, $v, false);
            }
        }
    }

}