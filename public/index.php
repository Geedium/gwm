<?php

!defined('GWM') ? define('GWM',
[
    'DIR_ROOT' => dirname(__DIR__),
    'START_TIME' => microtime(true),
    'ERROR_LEVEL' => error_reporting(E_ALL)
]) : exit;

chdir(GWM['DIR_ROOT']);

require 'Core.php';

$reader2 = new GWM\Core\Reader('templates/Security.html');

$reader = new GWM\Core\Reader('templates/Dependencies.html');
$reader->Merge('{{ dependencies }}', $reader2);

$router = new GWM\Core\Router();

$router->Match('/dashboard', function() {
    $request = new GWM\Core\Request();
    $dash = new GWM\Core\Controllers\Dashboard();
    $dash->index($request);
});

//echo $reader;

?>