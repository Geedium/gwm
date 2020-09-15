<?php

namespace GWM\Core\Controllers;

class Home
{
    public function index()
    {
        $schema = new \GWM\Core\Schema('test_app');

        $model = new \GWM\Core\Models\Article($schema);
        $articles = $model->Select($schema);

        $latte = new \Latte\Engine;
        $latte->setTempDirectory('tmp/latte');

        $params = [
            'articles' => $articles
        ];

        $latte->render('themes/default/templates/index.latte', $params);
    }
}