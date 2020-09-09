<?php

namespace GWM\Core\Controllers;

class Dashboard
{
    public function index($request)
    {
        try {
            $schema = new \GWM\Core\Schema('test_app');
            $model = new \GWM\Core\Models\User($schema);
        } 
        catch(\Exception $e) {}

        $view = new \GWM\Core\Views\Container;
        $view->index('.html', 'admin');
    }

    public function media()
    {
        $files = [];

        if ($handle = opendir('public/uploads/')) {
            while ($file = readdir($handle)) {
                $files[] = $file;
            }
        }

        sort($files);

        $fm = new \GWM\Core\Views\FileManager;
        $fm->index($files);
    }
}