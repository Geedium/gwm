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
        $public = GWM['DIR_ROOT'] . '/public';
        $themes = GWM['DIR_ROOT'] . '/themes';

        $theme = "$themes/admin/styles";

        if(file_exists("$public/css/dashboard.generated.css") == true) {
            if(!unlink("$public/css/dashboard.generated.css")) {
                throw \Exception('failed to unlink file');
            }
        }

        $sass = "sass $theme/_index.sass:$public/css/dashboard.generated.css --style compressed";

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

        Utils::exec($sass);

        header('Location: /dashboard', true);
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

        $dir = GWM['DIR_ROOT'];

        echo Utils::exec("tsc --project $dir");
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