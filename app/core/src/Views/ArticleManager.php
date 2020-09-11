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

    function index($articles)
    {
        $reader = new \GWM\Core\Reader('app/core/themes/default/templates/default/layout.tpl');
        $reader->Replace('{{ assetPath }}', $this->detach());

        $view = new \GWM\Core\Reader('app/core/themes/default/templates/default/manageArticles.tpl');

        $i = 0;
        $first = null;

        foreach ($articles as $article) {
            if ($i == 0) {
                $first = new \GWM\Core\Reader('app/core/themes/default/templates/default/article.tpl');
                $first->Replace('<!--ID-->', $article['id']);
                $first->Replace('<!--Title-->', $article['title']);
                $first->Replace('<!--Content-->', $article['content']);
                $first->Replace('<!--Date-->', $article['created_at']);
            } else {
                $other = new \GWM\Core\Reader('app/core/themes/default/templates/default/article.tpl');
                $other->Replace('<!--ID-->', $article['id']);
                $other->Replace('<!--Title-->', $article['title']);
                $other->Replace('<!--Content-->', $article['content']);
                $other->Replace('<!--Date-->', $article['created_at']);
                $first->Concatenate($other);
                $other = null;
            }
            $i += 1;
        }

        $view->Replace('<!--Articles-->', $first);

        $reader->Replace('<!--Content-->', $view);

        echo $reader;
    }
}