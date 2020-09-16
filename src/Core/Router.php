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

    function Resolve()
    {
        $this->Match('/', function() {
            $home = new \GWM\Core\Controllers\Home();
            $home->index();
            exit;
        });
        
        $this->Match('/store', function() {
            $home = new \GWM\Commerce\Controllers\Store();
            $home->index();
            exit;
        });
        
        $this->Match('/dashboard', function() {
            $request = new Request();
            $dash = new Controllers\Dashboard();
            $dash->index($request);
            exit;
        });
        
        $this->Match('/auth', function() {
            $auth = new Controllers\Auth();
            $auth->index();
            exit;
        });
        
        $this->Match('/api/articles', function() {
            $dash = new Controllers\Dashboard();
            $dash->articles();
            exit;
        });
        
        $this->Match('/dashboard/build', function() {
            $dist = new Distributor;
            exit;
        });
        
        $this->Match('/dashboard/articles', function() {
            $dash = new Controllers\Dashboard();
            $dash->articles();
            exit;
        });
        
        $this->Match('/dashboard/media', function() {
            $dash = new GWM\Core\Controllers\Dashboard();
            $dash->media();
            exit;
        });
    }

    function Match($url, $function)
    {
        if ($_SERVER['REQUEST_URI'] == $url) {
            $function->__invoke();
        }
    }
}