<?php

namespace GWM\Core\Controllers;

class Dashboard
{
    public function index($request)
    {
        $view = new \GWM\Core\Views\Container;
        $view->index();
    }
}