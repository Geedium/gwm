<?php

namespace GWM\Core\Views;

class Container
{
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