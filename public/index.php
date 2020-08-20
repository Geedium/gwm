<?php

!defined('GWM') ? define('GWM',
[
    'DIR_ROOT' => dirname(__DIR__),
    'START_TIME' => microtime(true),
    'ERROR_LEVEL' => error_reporting(E_ALL)
]) : exit;

chdir(GWM['DIR_ROOT']);

require 'Core.php';
require 'Core/Composer/index.php';

$dotenv = Dotenv\Dotenv::createImmutable(GWM['DIR_ROOT']);

$dotenv->load();

$dotenv->required([
    'DB_DRIVER',
    'DB_HOST',
    'DB_USERNAME',
    'DB_PASSWORD'
]);

$schema = new Schema('test_app');

//$reader2 = new GWM\Core\Reader('templates/Security.html');

//$reader = new GWM\Core\Reader('templates/Dependencies.html');
//$reader->Merge('{{ dependencies }}', $reader2);

$router = new GWM\Core\Router();

$router->Match('/', function() {
    $home = new GWM\Core\Controllers\Home();
    $home->index();
    exit;
});

$router->Match('/dashboard', function() {
    $request = new GWM\Core\Request();
    $dash = new GWM\Core\Controllers\Dashboard();
    $dash->index($request);
    exit;
});

$response = new Response();
$response->setContent('Page was not found. (404)')->send(404);

//echo $reader;

?>