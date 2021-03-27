<?php

namespace GWM\Core {

    use GWM\Core\Errors\Basic;

    /**
     * Class Distributor
     *
     * This class provides user-interface to user.
     *
     * @internal
     * @package GWM\Core
     * @version 1.0.0
     */
    class Distributor
    {
        protected string $theme;

        /**
         * Distributor constructor.
         * @param string $name Theme name.
         * @throws Basic
         */
        function __construct(string $name = 'admin')
        {
            $this->Context($name);

            $public = GWM['DIR_ROOT'] . '/public';
            $themes = GWM['DIR_ROOT'] . '/themes';

            $theme = "$themes/admin/styles";

            if (file_exists("$public/css/dashboard.generated.css") == true) {
                if (!unlink("$public/css/dashboard.generated.css")) {
                    throw new Basic('failed to unlink file');
                }
            }

            $sass = "sass $theme/_index.sass:$public/css/dashboard.generated.css --style compressed";

            Utils::exec($sass);

            header('Location: /dashboard', true);
        }

        /**
         * Function Context
         *
         * Validates and obtains theme.
         *
         * @param string $name Theme name.
         * @return Distributor
         */
        public function Context(string $name) : Distributor
        {
            $this->theme = $name;
            return $this;
        }

        /**
         * Undocumented function
         *
         * @return boolean
         * @since 1.0.0
         */
        public static function node_installed(): bool
        {
            $v = Utils::exec('node --version');
            return substr($v, 0, 1) === 'v';
        }

        /**
         * Undocumented function
         *
         * @return void
         * @since 1.0.0
         */
        protected function compile_typescript()
        {
            chdir(GWM['DIR_ROOT']);

            $dir = GWM['DIR_ROOT'];

            echo Utils::exec("tsc --project $dir");
        }

        function __destruct()
        {
            chdir(GWM['DIR_ROOT']);
        }

        static function Json(string $filename)
        {
            \ob_start();
            @readfile("$filename.json");
            return json_decode(\ob_get_clean());
        }
    }
}