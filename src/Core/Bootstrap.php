<?php

namespace GWM\Core {

    use Dotenv\Dotenv;

    /**
     * Class Bootstrap
     *
     * No description.
     *
     * @internal
     * @deprecated
     * @package GWM\Core
     * @version 1.0.0
     */
    class Bootstrap
    {
        public const DEBUG_MODE = true;

        /**
         * Bootstrap constructor.
         * @magic
         */
        public function __construct()
        {
            defined('GWM') or exit;



            $this->Configuration();
            $this->Environment();

            Plugin::Load();
        }

        function Configuration()
        {
            $sapi_type = php_sapi_name();
            if (substr($sapi_type, 0, 3) == 'cgi') {
                ini_set('max_execution_time', 0);
                ini_set('max_input_time', -1);
            }
        }

        private function Environment()
        {

        }
    }
}