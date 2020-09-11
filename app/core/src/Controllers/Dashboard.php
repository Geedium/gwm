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

    /**
     * @method GET
     */
    public function articles()
    {
        if(\filter_has_var(INPUT_POST, 'title')
        && \filter_has_var(INPUT_POST, 'content')
        && !\filter_has_var(INPUT_POST, 'id'))
        {
            try {
                $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
                $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);

                $schema = new \GWM\Core\Schema('test_app');
                
                $model = new \GWM\Core\Models\Article($schema);
                $model->setTitle($title);
                $model->setContent($content);
                $model->Create($schema);

                $response = new \GWM\Core\Response();
                $json = [
                    'success' => true
                ];
                $response->sendJson($json, 201);

            } catch (\Exception $e) {
                \trigger_error($e->getMessage, E_USER_ERROR);
            }
        }
        else if(\filter_has_var(INPUT_POST, 'id'))
        {
            try {
                $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
                $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
                $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

                $schema = new \GWM\Core\Schema('test_app');
                
                $model = new \GWM\Core\Models\Article($schema);
                $model->setTitle($title);
                $model->setContent($content);
                $model->Save($schema, $id);

                $response = new \GWM\Core\Response();
                $response->setContent('')->send(201);

            } catch (\Exception $e) {
                \trigger_error($e->getMessage, E_USER_ERROR);
            }
        }
        else {
            $schema = new \GWM\Core\Schema('test_app');
                
            $model = new \GWM\Core\Models\Article($schema);
            $articles = $model->Select($schema);

            $article = new \GWM\Core\Views\ArticleManager;
            $article->index($articles);
        }
    }
}