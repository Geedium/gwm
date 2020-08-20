<?php

namespace GWM\Core\Controllers;

class Home
{
    public function index()
    {
        $view = new \GWM\Core\Views\Container;
        $view->index('.pug');
    }
}