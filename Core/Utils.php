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

    /*
    function _autoload($path, $extensions, $flags = 0)
{
    $iterations = new FilesystemIterator($fp, $flags);
    foreach ($iterations as $iteration) {
        $extension = $fp->getExtension();
        
        if (in_array($extension, $extensions) == false) {
            continue;
        }

        $basename = $fp->getBasename($extension);
        include_once($path.'/'.$fp->getFilename());
    }
} */


}