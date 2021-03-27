<?php

namespace GWM\Core\Template\Engines {

    use GWM\Core\Interfaces\ITemplateEngine;

    use GWM\Core\{Bootstrap, Session, Singleton};

    use GWM\Forum\Context;
    use Twig\Environment;
    use Twig\Loader\FilesystemLoader;

    use Twig\Error\ {
        LoaderError,
        RuntimeError,
        SyntaxError
    };
    use Twig\TwigFunction;

    /**
     * Class Twig
     * @package GWM\Core\Template\Engines
     * @version 1.0.0
     */
    class Twig extends Singleton implements ITemplateEngine
    {
        private Environment $engine;
        private $context;
        private $path;

        private static array $lang = [];

        protected function Init(): void
        {
            $loader = new FilesystemLoader(GWM['DIR_ROOT'] . '/themes');

            $this->engine = new Environment($loader, [
                'cache' => GWM['DIR_ROOT'] . '/tmp/twig',
                'debug' => Bootstrap::DEBUG_MODE
            ]);

            $function = new TwigFunction('this', function ($target) use ($path) {
                return $this->path.'/'.$target;
            });

            $function = new TwigFunction('Logged', function () {
                return Session::Get()->Logged();
            });

            $this->engine->addFunction($function);
        }

        function setContext($context): void
        {
            $this->context = $context;
        }

        function Mount(string $path)
        {
            $this->path = $path;
            return $this;
        }

        function Parse(string $path, array $params = []): string
        {
            try {
                return $this->engine->render($this->$path.'/'.$path, $params);
            } catch (LoaderError $e) {
                die($e->getMessage());
            } catch (RuntimeError $e) {
                die($e->getMessage());
            } catch (SyntaxError $e) {
                die($e->getMessage());
            }
        }
    }
}