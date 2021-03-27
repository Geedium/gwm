<?php

if (defined('GWM') == false) {
    http_response_code(500);
    exit;
}

if (!@is_dir(GWM['DIR_PUBLIC'].'/vendor') == true) {
    $zip = new ZipArchive;
    $res = $zip->open(GWM['DIR_VENDOR'].'/1604630425.zip');
    if ($res === true) {
        $zip->extractTo(GWM['DIR_PUBLIC'].'/');
        $zip->close();
    }
}

if (!@include_once 'vendor/autoload.php') {
    if (file_exists('composer.json') == false) {
        trigger_error('Composer package file not found!', E_USER_ERROR);
    }

    $composer_version = shell_exec('composer --version 2>&1');

    if (substr($composer_version, 0, 8) !== 'Composer') {
        trigger_error('Your host should have Composer globaly installed.', E_USER_NOTICE);
        $zip = new ZipArchive;
        $res = $zip->open(GWM['DIR_VENDOR'].'/1604629210.zip');
        if ($res === true) {
            $zip->extractTo(GWM['DIR_VENDOR'].'/');
            $zip->close();

            echo 'Pre-installed!';
        } else {
            echo 'Install failed!';
        }
    } else {
        shell_exec('composer -n update');
        trigger_error('Composer update executed, restart page after several minutes.');
    }
    
    exit;
}

$composer = json_decode(file_get_contents('composer.json'), true)['require'];