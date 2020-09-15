<?php

namespace GWM\Core\Views;

class FileManager
{
    function index(array $files)
    {
        $container = new \GWM\Core\Views\Container;

        $output = new \GWM\Core\Reader();

        foreach ($files as $key => $value) {
            echo $key.','.$value.'<br/>';
        }

        for ($i = 0; $i < sizeof($files); $i++) {
            if ($files[$i] != '.' && $files[$i] != '..') {
                $reader = new \GWM\Core\Reader('app/core/themes/default/templates/default/file.tpl');
                $reader->Replace('{% file %}', $files[$i]);

                $output->Concatenate($reader);
            }
        }

        $containerView = $container->layout('.html', 'admin');

        $containerView->Merge('Content', $output);

        echo $containerView;
    }
}