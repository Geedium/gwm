<?php

!defined('GWM') ? define('GWM',
[
    'DIR_ROOT' => dirname(__DIR__),
    'START_TIME' => microtime(true),
    'ERROR_LEVEL' => error_reporting(E_ALL)
]) : exit;

if(version_compare(PHP_VERSION, '7.0.0') < 0) exit;

chdir(GWM['DIR_ROOT']);

require_once 'vendor/index.php';

chdir(GWM['DIR_ROOT']);

if (file_exists('.env') == false) {
    $generated = bin2hex(random_bytes(5));

    file_put_contents('.env', <<<EOF
    DB_DRIVER=
    DB_HOST=
    DB_USERNAME=
    DB_PASSWORD=
    DB_PREFIX=$generated
    EOF);
    
    trigger_error('You need to update .env variables!');
    exit;
}

$dotenv = Dotenv\Dotenv::createImmutable(GWM['DIR_ROOT']);

$dotenv->load();

$dotenv->required([
    'DB_DRIVER',
    'DB_HOST',
    'DB_USERNAME',
    'DB_PASSWORD',
    'DB_NAME',
    'DB_PREFIX'
]);

$router = new GWM\Core\Router();

$router->Match('/', function() {
    $home = new \GWM\Core\Controllers\Home();
    $home->index();
    exit;
});

$router->Match('/store', function() {
    $home = new \GWM\Commerce\Controllers\Store();
    $home->index();
    exit;
});

$router->Match('/dashboard', function() {
    $request = new GWM\Core\Request();
    $dash = new GWM\Core\Controllers\Dashboard();
    $dash->index($request);
    exit;
});

$router->Match('/auth', function() {
    $auth = new GWM\Core\Controllers\Auth();
    $auth->index();
    exit;
});

$router->Match('/api/articles', function() {
    $dash = new GWM\Core\Controllers\Dashboard();
    $dash->articles();
    exit;
});

$router->Match('/dashboard/build', function() {
    $dist = new \GWM\Core\Distributor;
    exit;
});

$router->Match('/dashboard/articles', function() {
    $dash = new GWM\Core\Controllers\Dashboard();
    $dash->articles();
    exit;
});

$router->Match('/dashboard/media', function() {
    $dash = new GWM\Core\Controllers\Dashboard();
    $dash->media();
    exit;
});

$response = new GWM\Core\Response();

$latte = new \Latte\Engine;
$latte->setTempDirectory('tmp/latte');
$err404 = $latte->renderToString('themes/default/templates/404.latte');

/*
$fp = fopen("render.lock", "a+");

if (flock($fp, LOCK_EX)) {  // acquire an exclusive lock
    ftruncate($fp, 0);      // truncate file
    fwrite($fp, $err404);
    fflush($fp);            // flush output before releasing the lock
    flock($fp, LOCK_UN);    // release the lock
} else {
    echo "Couldn't get the lock!";
}

fclose($fp);
*/

$response->setContent($err404)->send(404);

?>