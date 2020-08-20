<?php

namespace GWM\Core\Views;

class Container
{
    function index($engine = '.pug')
    {
        if ($engine == '.pug') {
            \GWM\Core::disable();

            global $composer;
            if ($composer['pug-php/pug'] != null) {
                
                $pug = new \Pug([
                    'pretty' => false,
                    'cache' => '.cache'
                ]);

                echo $pug->render('Core/Assets/templates/default/template.pug', [
                    
                ]);
            }

            \GWM\Core::enable();
        }
        else {
            $reader = new \GWM\Core\Reader('Core/Assets/templates/default/index.tpl');
            echo $reader;
        }
    }
}