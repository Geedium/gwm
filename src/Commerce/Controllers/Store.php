<?php

namespace GWM\Commerce\Controllers;

class Store
{
    public function index()
    {
        $latte = new \Latte\Engine;
        $latte->setTempDirectory('tmp/latte');
        $latte->render('themes/default/templates/store.latte');
    }
}