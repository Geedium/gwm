<?php

namespace GWM\Core {
    use \GWM\Mapper;

    /**
     * Class App
     * @package GWM\Core
     * @version 1.0.0
     */
    class App
    {
        const DEBUG_MODE = true;

        function __construct()
        {
            register_shutdown_function([__CLASS__, 'Shutdown']);
        }
        
        /**
         * Function Shutdown
         * @since 1.0.0
         */
        public static function Shutdown()
        {
            if (PHP_SAPI == "cgi" || PHP_SAPI == "cgi-fcgi") {
                fastcgi_finish_request();
            }
        }

        /**
         * Function Hash
         * @param int $type Operation to perform.
         * @param string $data Data to hash.
         * @return false|string Returns hashed data on success.
         * @since 1.0.0
         */
        public static function Hash(int $type, string $data)
        {
            switch ($type) {
                default:
                    $tmp = hash('sha512/256', $data, true);
                    $tmp = bin2hex($tmp);
                    return substr($tmp, 0, 10);
            }
        }

        public static function Pair(...$params)
        {

        }
    }
}