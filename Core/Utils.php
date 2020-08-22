<?php

namespace GWM\Core;

class Utils
{
    /**
     * Execute
     * 
     * Executes a command via shell
     * with additional options.
     *
     * @param [type] $cmd
     * @return mixed
     */
    static function exec($cmd) 
    {
        return \shell_exec("$cmd 2>&1");
    }
}