<?php

namespace GWM\Core\Controllers;

class Dashboard
{
    public function index($request)
    {
        $schema = new \GWM\Core\Schema('test_app');

        $model = new \GWM\Core\Models\User($schema);

        $view = new \GWM\Core\Views\Container;
        $view->index('.pug', 'admin');
    }
}