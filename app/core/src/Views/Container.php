<?php

namespace GWM\Core\Views;

class Container
{
    function detach()
    {
        $paths = explode('/', $_SERVER['REQUEST_URI']);
        array_shift($paths);

        $root = GWM['DIR_ROOT'];
        $level = '';

        foreach ($paths as $path) {
            $up = "$root/$path";

            if (is_dir($up)) {
                continue;
            } else {
                $level .= '..\\';
            }
        }

        return $level;
    }

    function index($engine = '.pug', $view = null)
    {
        if ($engine == '.pug') {
            \GWM\Core::disable();

            global $composer;
            if ($composer['pug-php/pug'] != null) {
                $pug = new \Pug([
                    'pretty' => true,
                    'cache' => '.cache/pug'
                ]);

                if ($view != null) {
                    echo $pug->render('app/core/themes/default/templates/a1/template.dashboard.pug', [
                        
                    ]);
                }
                else {
                    echo $pug->render('app/core/themes/default/templates/default/template.pug', [
                        
                    ]);
                }
            }

            \GWM\Core::enable();
        } else {
            $reader = new \GWM\Core\Reader('app/core/themes/default/templates/default/index.tpl');
            $reader->Replace('{{ assetPath }}', $this->detach());
            echo $reader;
        }
    }

    function layout($engine = '.pug', $view = null)
    {
        if ($engine == '.pug') {
            \GWM\Core::disable();

            global $composer;
            if ($composer['pug-php/pug'] != null) {
                $pug = new \Pug([
                    'pretty' => true,
                    'cache' => '.cache/pug'
                ]);

                if ($view != null) {
                    return $pug->render('app/core/themes/default/templates/a1/template.dashboard.pug', [
                        
                    ]);
                }
                else {
                    return $pug->render('app/core/themes/default/templates/default/template.pug', [
                        
                    ]);
                }
            }

            \GWM\Core::enable();
        } else {
            $reader = new \GWM\Core\Reader('app/core/themes/default/templates/default/layout.tpl');
            return $reader;
        }
    }


}