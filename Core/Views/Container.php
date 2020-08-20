<?php

namespace GWM\Core\Views;

class Container
{
    function index($model = null)
    {
        if (GWM['USE_COMPOSER']) {
            \GWM\Core::disable();

            global $composer;
            if ($composer['pug-php/pug'] != null) {
                $pug = new \Pug([
                    
                ]);

                $pug->displayFile('Core/Assets/templates/default/template.pug');
            }

            \GWM\Core::enable();
        }
        else {
            $reader = new \GWM\Core\Reader('Core/Assets/templates/default/index.tpl');
            echo $reader;
        }
    }
}