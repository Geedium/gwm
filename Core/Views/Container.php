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
                    echo $pug->render('Core/Assets/Templates/a1/template.dashboard.pug', [
                        
                    ]);
                }
                else {
                    echo $pug->render('Core/Assets/templates/default/template.pug', [
                        
                    ]);
                }
            }

            \GWM\Core::enable();
        } else {
            $reader = new \GWM\Core\Reader('Core/Assets/templates/default/index.tpl');
            echo $reader;
        }
    }


}