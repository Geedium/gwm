<?php

chdir(__DIR__);

require 'vendor/autoload.php';

$composer = json_decode(file_get_contents('composer.json'), true)['require'];

chdir(GWM['DIR_ROOT']);