<?php

namespace GWM\Core;

/**
 * Undocumented class
 */
class Router
{
    protected $routes = [];

    /**
     * @magic
     */
    function __construct()
    {
        $url = filter_var(rtrim($_SERVER['REQUEST_URI'], '/'), FILTER_SANITIZE_URL);

        //$json = \file_get_contents('routes.json');
        //$data = \json_decode($json);

         // $this->routes = $data;

        $URI = explode('/', $url);
        $URI[0] = $_SERVER['REQUEST_METHOD'];
    }

    function Match($url, $function)
    {
        if ($_SERVER['REQUEST_URI'] == $url) {
            $function->__invoke();
        }
    }
}