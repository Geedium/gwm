<?php

use GWM\Core\Template\Engine;
use GWM\Core\Plugin;

/**
 * Author: Gediminas
 * Name: Plugin
 * Version: 1.0.0
 */

class Manipulatable extends \GWM\Core\Plugin
{
    function __construct()
    {
        parent::__construct($this, 'Social Buttons');
    }

    public function Manipulate(string &$renderOutput)
    {
        /*
        if (strpos($_SERVER['REQUEST_URI'], 'dashboard') > 0) {
            return;
        }

        try {
            $html = Engine::Get('twig')->Parse('a0/templates/plugins/social_buttons/index.html.twig');
            $css = Engine::Get('twig')->Parse('a0/templates/plugins/social_buttons/index.css.twig');
        } catch (Exception $e) {
            $html = $e->getMessage();
        }

        $renderOutput = str_replace('</head>', '<style>' . $css . '</style>', $renderOutput);
        $renderOutput = str_replace('</body>', $html . '</body>', $renderOutput);
        */
    }
}

new Manipulatable();