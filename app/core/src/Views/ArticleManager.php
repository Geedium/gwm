<?php

namespace GWM\Core\Views;

class ArticleManager
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
        $reader = new \GWM\Core\Reader('app/core/themes/default/templates/default/layout.tpl');
        $reader->Replace('{{ assetPath }}', $this->detach());

        $view = new \GWM\Core\Reader('app/core/themes/default/templates/default/manageArticles.tpl');
        $reader->Replace('<!--Content-->', $view);

        echo $reader;
    }
}