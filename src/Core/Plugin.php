<?php

namespace GWM\Core;

abstract class Plugin
{
    public static $plugins = [];
    private static bool $loaded = false;

    private static function Add($object, $name, $order = 1, $options = null)
    {
        self::$plugins[] = [
            'ref' => $object,
            'order' => $order,
            'options' => $options,
            'identifier' => $name
        ];

        \usort(self::$plugins, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });
    }

    function __construct($object, $name)
    {
        self::Add($object, $name);
    }

    static function Ensure(): void
    {
        if (!self::$loaded) {
            self::Load();
        }
    }

    static function Header($file)
    {
        $docComments = array_filter(
            token_get_all($file), function($entry) {
            return $entry[0] == T_DOC_COMMENT;
        }
        );
        $fileDocComment = array_shift( $docComments );
        return $fileDocComment[1];
    }

    static function Load()
    {
        self::$loaded = true;

        $io_dir = array_diff(scandir(GWM['DIR_ROOT'] . '/plugins'),
            array('.', '..'));

        foreach($io_dir as $key) {
            if (is_dir(GWM['DIR_ROOT'] . "/plugins/$key/src")) {
                $dirs = array_diff(scandir(GWM['DIR_ROOT'] . "/plugins/$key/src"),
                    array('.', '..'));

                // HEADER READ
                $filename = GWM['DIR_ROOT'] . "/plugins/$key/src/Entry.php";

                $handle = fopen($filename, 'r');
                $header = stream_get_line($handle, 2500, '*/');
                fclose($handle);

                @include $filename;
            }
        }
    }

    public abstract function Manipulate(string &$renderOutput);
}