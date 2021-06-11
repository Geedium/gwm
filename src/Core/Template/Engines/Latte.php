<?php

namespace GWM\Core\Template\Engines {

    use GWM\Core\Singleton;
    use GWM\Core\Session;
    use GWM\Core\Interfaces\ITemplateEngine;
    use GWM\Core\UI\Google;

    use Latte\Engine;

    /**
     * Class Latte
     * @package GWM\Core\Template\Engines
     * @version 1.0.0
     */
    class Latte extends Singleton implements ITemplateEngine
    {
        private Engine $engine;

        private static array $lang = [];

        private static array $data = [];

        protected function Init(): void
        {
            $this->engine = new Engine();
            $this->engine->setTempDirectory('tmp/latte');

            $this->engine->addFilter('trans', function ($s) {
                return $s;
            });

            $this->engine->addFunction('css', function ($s) {
                if(!file_exists(GWM['DIR_ROOT'].'/tmp/gwm/entrypoints.json'))
                    return '/css/'.$s.'.css';

                $json = json_decode(file_get_contents(GWM['DIR_ROOT'].'/tmp/gwm/entrypoints.json'), true);
                if(isset($json[$s]))
                {
                    return '/css/'.$json[$s];
                }

                return '/css/'.$s.'.css';
            });

            $this->engine->addFunction('trans', function ($s) {
                return self::Translate($s);
            });

            $this->engine->addFunction('alerts', function() {
                if (isset($_SESSION['messages']) && is_array($_SESSION['messages'])) {
                    self::$data = $_SESSION['messages'];
                    unset($_SESSION['messages']);

                    return sizeof(self::$data);
                }
                return 0;
            });
            
            $this->engine->addFunction('alert', function() {
                return array_pop(self::$data);
            });

            $this->engine->addFunction('takeover', function(string $rs): string {
                $name = substr($rs, stripos($rs, '/') + 1);
                $rs = substr($rs, 0, stripos($rs, '/'));
                return GWM['DIR_ROOT']."/resources/$rs/templates/$name";
            });

            $this->engine->addFunction('is_user_moderator', function($user) {
                $auth = new \GWM\Core\Services\Auth($user);
                return $auth->has_role('posts.moderator');
            });

            $this->engine->addFunction('snp_google_btn', function(string $auth_url = null) {
                return Google::com_button([
                    'href' => $auth_url
                ]);
            });

            $this->engine->addFunction('logged', function() {
                return Session::Get()->Logged();
            });

            self::$lang = include_once(GWM['DIR_ROOT'] . '/src/Core/Template/Locales/lt_LT/global.php');
        }

        function Translate(string $s): string
        {
            return self::$lang[$s] ?? $s;
        }

        function Parse(string $path, array $params = []): string
        {
            return $this->engine->renderToString($path, $params);
        }
    }
}