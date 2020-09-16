<?php

defined('GWM') or exit;

if (!@include_once 'vendor/autoload.php') {
    if (file_exists('composer.json') == false) {
        trigger_error('Composer package file not found!', E_USER_ERROR);
        exit;
    }

    $composer_version = shell_exec('composer --version 2>&1');

    if (substr($composer_version, 0, 8) !== 'Composer') {
        trigger_error('You need to download Composer to continue.', E_USER_ERROR);
    }

    shell_exec('composer update');
    trigger_error('Composer update executed, restart page after several minutes.');
    exit;
}

$composer = json_decode(file_get_contents('composer.json'), true)['require'];