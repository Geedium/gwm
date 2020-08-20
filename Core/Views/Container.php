<?php

namespace GWM\Core\Views;

class Container
{
    function index($model = null)
    {
        $response = new \GWM\Core\Response();
        return $response->setContent('Dashboard!')->send(200);
    }
}