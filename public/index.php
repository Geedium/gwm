<?php

!defined('GWM') ? define('GWM',
[
    'DIR_ROOT' => dirname(__DIR__),
    'START_TIME' => microtime(true),
    'ERROR_LEVEL' => error_reporting(E_ALL)
]) : exit;

chdir(GWM['DIR_ROOT']);

require 'Core.php';

$response = new Response();
$response->setContent('Hello world!')->send(200)

?>