<?php

if (defined('GWM') == false) {
    http_response_code(500);
    exit;
}

if (!@include_once 'vendor/autoload.php') {
    if (file_exists('composer.json') == false) {
        trigger_error('Composer package file not found!', E_USER_ERROR);
    }

    $composer_version = shell_exec('composer --version 2>&1');

    if (substr($composer_version, 0, 8) !== 'Composer') {
        trigger_error('You need to download Composer to continue.', E_USER_ERROR);
    }

    shell_exec('composer -n update');
    trigger_error('Composer update executed, restart page after several minutes.');
    exit;
}

$composer = json_decode(file_get_contents('composer.json'), true)['require'];