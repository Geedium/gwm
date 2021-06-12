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

if(!file_exists('vendor/autoload.php')) {

    while (@ ob_end_flush()); // end all output buffers if any

    $cmd = 'composer install';

    $proc = popen("$cmd 2>&1", 'r');

    $live_output     = "";
    $complete_output = "";

    echo '<pre>';
    while (!feof($proc))
    {
        $live_output     = fread($proc, 4096);
        $complete_output = $complete_output . $live_output;
        echo "$live_output";
        @ flush();
    }
    echo '</pre>';

    pclose($proc);

     // get exit status
     preg_match('/[0-9]+$/', $complete_output, $matches);

     $result = [
        'exit_status'  => intval($matches[0]),
        'output'       => str_replace("Exit status : " . $matches[0], '', $complete_output)
     ];

     if($result['exit_status'] === 0){
        // do something if command execution succeeds
        
        $key = bin2hex(random_bytes(16));

        if (!file_exists('INSTALL.txt')) {
            file_put_contents('INSTALL.txt', $key);
        } else {
            throw new Error('Security mismatch! Remove INSTALL.txt and try again.');
        }

        $url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'];
        var_dump("$url/?key=$key");

        ob_start();
        
        header("Location: $url/?key=$key");

        if (!ob_end_flush()) {
            die('Output buffering failed.');
        }

        exit;
     } else {
         // do something on failure
         throw new Error('Failed to install dependencies!');
     }
}

require_once('vendor/autoload.php');

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