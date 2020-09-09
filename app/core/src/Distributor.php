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

    /**
     * Undocumented function
     *
     * @param string $src
     * @param string $dest
     * @param integer $quality
     * @return string
     */
    static function Compress(string $src, string $dest, int $quality = 100) : string
    {
        \getimagesize($src, $info);

        switch ($info['mime']) {
            case 'image/jpeg': $image = imagecreatefromjpeg($src); break;
            case 'image/gif': $image = imagecreatefromgif($src); break;
            case 'image/png': $image = imagecreatefrompng($src); break;
        }

        imagejpeg($image, $dest, $quality);
        return $dest;
    }
}