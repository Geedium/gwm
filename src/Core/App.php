<?php

namespace GWM\Core {
    use \GWM\Mapper;
    use \GWM\Core\Response;

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
            if(self::DEBUG_MODE)
            {
                \error_reporting(E_ALL);
            }

            register_shutdown_function([__CLASS__, 'Shutdown']);
        }

        public static function finalize_request(Request $request)
        {
            $request->handle_middleware();
        }
        
        /**
         * Function Shutdown
         * @since 1.0.0
         */
        public static function Shutdown()
        {
            if (self::DEBUG_MODE) {
                //$current_user = trim(shell_exec('whoami'));
                //var_dump(\debug_backtrace());
            }

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