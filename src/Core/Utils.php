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
     * @return mixed Result.
     */
    static function exec($cmd) 
    {
        return \shell_exec("$cmd 2>&1");
    }

    /**
     * Get Multipurpose Internet Mail Extensions Type
     * 
     * Converts file extension to multipurpose
     * internet mail extension type.
     *
     * @param string $extn File Extension
     * @return string MIME Type
     */
    public static function get_mime_type(string $extn): string
    {
        switch ($extn) {
            case 'png': return 'image/png'; // 	Portable Network Graphics
            case 'zip': return 'application/zip'; // ZIP archive
            case 'rar': return 'application/vnd.rar'; // RAR archive
            case 'mp3': return 'audio/mpeg'; // MP3 audio
            case 'mp4': return 'video/mp4'; // MPEG-4
            case 'jpg': case 'jpeg': return 'image/jpeg'; // JPEG images
            case 'js': return 'text/javascript'; // JavaScript
            case 'json': return 'application/json'; // JSON format
            case 'mjs': return 'text/javascript'; // JavaScript module
            case 'webm': return 'video/webm'; // WEBM video
            case 'webp': return 'image/webp'; // WEBP image
            default: return 'application/octet-stream';
        }
    }

    static function Span(array $data, string $method): array
    {
        $length = sizeof($data);
        $min = $data[0]->{$method}();
        $max = $min;

        for ($i = 0; $i < $length; $i++) {
            if ($data[$i]->{$method}() > $max) {
                $max = $data[$i]->{$method}();
            }
            if ($data[$i]->{$method}() < $min) {
                $min = $data[$i]->{$method}();
            }
        }
        
        return [
            'max' => $max, 
            'min' => $min,
            'range' => $max - $min
        ];
    }
    
    /**
     * Undocumented function
     *
     * @param [type] $src
     * @return void
     */
    static function delete_dir($src)
    {
        $dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::delete_dir($src . '/' . $file);
                } else {
                    unlink($src . '/' . $file);
                }
            }
        }
        closedir($src);
        rmdir($src);
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