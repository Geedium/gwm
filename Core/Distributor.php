<?php

namespace GWM\Core;

/**
 * Undocumented class
 * 
 * @version 1.0.0
 */
class Distributor
{
    function __construct()
    {
        echo $this->compile_typescript();

        chdir(__DIR__.'\\Assets');
    }

    /**
     * Undocumented function
     *
     * @return boolean
     * @since 1.0.0
     */
    protected function node_installed() : bool
    {
        $v = Utils::exec('node --version');
        return substr($v, 0, 1) === 'v';
    }

    /**
     * Undocumented function
     *
     * @return void
     * @since 1.0.0
     */
    protected function compile_typescript()
    {
        chdir(GWM['DIR_ROOT']);

        Utils::exec('tsc');
    }

    function __destruct() 
    {
        chdir(GWM['DIR_ROOT']);
    }

    static function Json(string $filename)
    {
        \ob_start();
        @readfile("$filename.json");
        return json_decode(\ob_get_clean());
    }
}