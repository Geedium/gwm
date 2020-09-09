<?php

namespace GWM\Commerce\Controllers;

class Home
{
    public function index()
    {
        $response = new \GWM\Core\Response();
        echo $response->setContent("Store")->send(200);
    }
}