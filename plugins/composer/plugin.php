<?php

chdir(__DIR__);

interface IPlugin
{
    public function Load() : bool;
}

class Plugin implements IPlugin
{
    private function __construct()
    {
        $this->Load();
    }

    public function Load() : bool
    {
        echo 'loading Plugin...';
    }
}

if (@!include_once 'vendor/autoload.php') {
    trigger_error('Unable to load composer autoloader.', E_USER_WARNING);
}

$composer = json_decode(file_get_contents('composer.json'), true)['require'];

/*
return new class implements IPlugin {
    
};
*/