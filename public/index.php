<?php

use Dotenv\Dotenv;
use GWM\Core\Router;
use GWM\Core\Utils\Debug;

!defined('GWM') ? define('GWM',
[
    'VERSION' => '1.0.0',
    'PROD_ENV' => false,
    'DIR_ROOT' => dirname(__DIR__),
    'DIR_PUBLIC' => dirname(__DIR__).'/public',
    'DIR_TMP' => dirname(__DIR__).'/tmp',
    'DIR_VENDOR' => dirname(__DIR__).'/vendor',
    'START_TIME' => microtime(true),
    'ERROR_LEVEL' => error_reporting(E_ALL)
]) : exit;

if(version_compare(PHP_VERSION, '7.4.0') < 0) exit;

chdir(GWM['DIR_ROOT']);

function exception_handler($exception) {
    die("Exception Caught: ". $exception->getMessage() ."\n");
}

function dump(...$params)
{
    Debug::Dump(...$params);
}

function error_handler($errno, $errstr, $errfile, $errline)
{
    $errstr = htmlspecialchars($errstr);

    Debug::$log[] = "Error[$errno] - $errstr, Line - $errline, File - $errfile";
}

require_once 'vendor/autoload.php';

if (is_dir(GWM['DIR_TMP']) == false) {
    mkdir(GWM['DIR_TMP']);
}

chdir(GWM['DIR_ROOT']);

if (file_exists('.env') == false) {
    configureEnvironment(filter_input(INPUT_GET, 'key'), [
        'DB_DRIVER',
        'DB_HOST',
        'DB_USERNAME',
        'DB_PASSWORD',
        'DB_NAME',
        'DB_PREFIX',
        'FALLBACK_THEME'
    ]);
    exit;
} else {
    $dotenv = Dotenv::createImmutable(GWM['DIR_ROOT']);
    $dotenv->load();

    $dotenv->required([
        'DB_DRIVER',
        'DB_HOST',
        'DB_USERNAME',
        'DB_PASSWORD',
        'DB_NAME',
        'DB_PREFIX',
        'FALLBACK_THEME'
    ]);
}

new \GWM\Core\App();

set_error_handler('error_handler');
set_exception_handler('exception_handler');

chdir(GWM['DIR_ROOT']);

$response = new GWM\Core\Response();

$router = new GWM\Core\Router();
$router->Resolve($response);