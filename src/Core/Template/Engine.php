<?php

namespace GWM\Core\Template {

    use GWM\Core\Errors\Basic;
    use GWM\Core\Template\Engines\ {
        Twig,
        Latte
    };

    /**
     * Class Engine
     * @package GWM\Core\Template
     * @version 1.0.0
     */
    class Engine
    {
        const HEX = ['a', 'b', 'c', 'd', 'e', 'f'];

        /**
         * @param string $engine
         * @param int $theme
         * @return Latte|Twig|object
         * @throws \Exception
         */
        public static function Get(string $engine = 'latte', int $theme = 0)
        {
            /* @TODO: Sync with dB
            $a = $theme / 10;
            $b = $a % 10;

            $manifest = json_decode(
                file_get_contents(GWM['DIR_ROOT'] . '/themes/a0/theme.json')
                , true);
             */

            switch ($engine) {
                case 'latte':
                case 'latte/latte':
                    if (class_exists('Latte\Engine') == true) {
                        return Latte::Get();
                    } else {
                        throw new Basic('Latte is not installed.', true);
                    }
                case 'twig':
                    if (class_exists('Twig\Environment') == true) {
                        return Twig::Get();
                    } else {
                        throw new Basic('Twig is not installed.', true);
                    }
            }

            throw new Basic('No theme found!', true);
        }
    }
}