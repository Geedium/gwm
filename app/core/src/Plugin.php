<?php

namespace GWM\Core;

class Plugin
{
    private static $plugins = [];

    static function Add($name, $order = 1, $options = null)
    {
        self::$plugins = ['order' => $order, 'options' => $options];
        \usort(self::$plugins, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });
    }
}