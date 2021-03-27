<?php

declare(strict_types=1);
namespace GWM\Core\Utils;

/**
 * Undocumented class
 * 
 * @version 1.0.0
 */
class Image
{
    /**
     * Undocumented function
     *
     * @param string $src
     * @param string $dest
     * @param integer $quality
     * @return string
     */
    static function compress(string $src, string $dest, int $quality = 100) : string
    {
        \getimagesize($src, $info);
        
        var_dump($info['mime']);

        switch ($info['mime']) {
            case 'image/jpeg': $image = imagecreatefromjpeg($src); break;
            case 'image/gif': $image = imagecreatefromgif($src); break;
            case 'image/png': $image = imagecreatefrompng($src); break;
        }

        imagejpeg($image, $dest, $quality);
        return $dest;
    }
}