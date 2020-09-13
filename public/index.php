<?php

!defined('GWM') ? define('GWM',
[
    'DIR_ROOT' => dirname(__DIR__),
    'START_TIME' => microtime(true),
    'ERROR_LEVEL' => error_reporting(E_ALL)
]) : exit;

if (version_compare(PHP_VERSION, '7.0.0') < 0) {
    trigger_error('Your PHP version unreliable. Please update your PHP to atleast v7.0.0!');
    exit;
}

if (PHP_SAPI != 'cli-server') { 
  //  trigger_error('Run webserver from CLI.');
  //  exit;
}

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

$json_apps = file_get_contents('apps.json');
$apps = json_decode($json_apps);

foreach ($apps as $key => $value) {
    require_once "app/$key/index.php";
}

$dotenv = Dotenv\Dotenv::createImmutable(GWM['DIR_ROOT']);

$dotenv->load();

$dotenv->required([
    'DB_DRIVER',
    'DB_HOST',
    'DB_USERNAME',
    'DB_PASSWORD'
]);

$router = new GWM\Core\Router();

$router->Match('/', function() {
    $home = new \GWM\Core\Controllers\Home();
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
    $user = $_POST['username'];
    $pw = $_POST['password'];

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
$response->setContent('Page was not found. (404)')->send(404);

//echo $reader;

?>