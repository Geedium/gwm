<?php

namespace GWM\Core\Controllers;

class Home
{
    public function index()
    {
        $schema = new \GWM\Core\Schema('test_app');

        $model = new \GWM\Core\Models\User($schema);

        $view = new \GWM\Core\Views\Container;
        $view->index('.pug');
    }
}