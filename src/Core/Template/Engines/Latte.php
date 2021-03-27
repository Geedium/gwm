<?php

namespace GWM\Core\Template\Engines {

    use GWM\Core\Singleton;
    use GWM\Core\Session;
    use GWM\Core\Interfaces\ITemplateEngine;

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

        protected function Init(): void
        {
            $this->engine = new Engine();
            $this->engine->setTempDirectory('tmp/latte');

            $this->engine->addFilter('trans', [__CLASS__, 'Translate']);

            $this->engine->addFilter('bundle', [__CLASS__, 'Bundle']);

            $this->engine->addFunction('trans', function ($s) {
                return self::Translate($s);
            });

            $this->engine->addFunction('takeover', function(string $rs): string {
                $name = substr($rs, stripos($rs, '/') + 1);
                $rs = substr($rs, 0, stripos($rs, '/'));
                return GWM['DIR_ROOT']."/resources/$rs/templates/$name";
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

        function Bundle(string $s): string
        {
            $r = new \SplFileInfo('');
            return $r->getRealPath();
        }

        function Parse(string $path, array $params = []): string
        {
            return $this->engine->renderToString($path, $params);
        }
    }
}