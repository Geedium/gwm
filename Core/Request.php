<?php

namespace GWM\Core;

/**
 * Undocumented class
 */
class Request
{
    /**
     * @magic
     */
    public function __construct()
    {
        echo $_SERVER['REQUEST_URI'].'<br/>';
        $routes = file_get_contents("routes.json");
        $go = json_decode($routes);

        echo '<br/><pre>'.var_dump($go).'</pre><br/>';
    }
}