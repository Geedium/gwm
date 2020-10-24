<?php

namespace GWM\Core;

use GWM\Core\Exceptions\Basic;

/**
 * Class Bootstrap
 *
 * @package GWM\Core
 * @version 1.0.0
 */
class Bootstrap
{
    /**
     * Bootstrap constructor.
     * @magic
     * @throws Basic
     */
    function __construct()
    {
        $sapi_type = php_sapi_name();
        if (substr($sapi_type, 0, 3) == 'cgi') {
            ini_set('max_execution_time', 0);
            ini_set('max_input_time', -1);
        }

        try {
            $this->test();
        } catch(Basic $e) {
            
        }
    }

    function test()
    {
        throw new Basic('Test.');
    }
}