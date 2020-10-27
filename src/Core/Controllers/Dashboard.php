<?php

namespace GWM\Core\Controllers;

use GWM\Core\Exceptions\Basic;
use GWM\Core\Request;
use GWM\Core\Response;

class Dashboard
{
    public function index(Request $request)
    {
        try {
            $schema = new \GWM\Core\Schema('test_app');
            $model = new \GWM\Core\Models\User($schema);
        } catch (\Exception $e) {
            echo 'Error creating model.';
        }

        $latte = new \Latte\Engine;
        $latte->setTempDirectory('tmp/latte');
        $latte->render('themes/admin/templates/index.latte');
    }

    public function models(Response $response)
    {
        $latte = new \Latte\Engine;
        $latte->setTempDirectory('tmp/latte');

        $action = $_POST['action'] ?? false;

        if($action) {

            $filename = GWM['DIR_ROOT'] . '/src/Core/Models/NewModel.php';

            $fp = fopen($filename, "w+");

            $name = 'newmodel';
            $classname = ucfirst($name);

            if (flock($fp, LOCK_EX | LOCK_NB)) {  // acquire an exclusive lock
                ftruncate($fp, 0);      // truncate file
                fwrite($fp, '
<?php
   
   class ' . $classname . '
   {
        /**
         * Undocumented variable
         *
         * @var string (255)
         */
        public string $title;
        
        /**
         * @magic
         * @param $schema
         */
        function __construct($schema)
        {
            $schema->Create(' . $classname . '::class, "' . $name . '");
        }
   }
');
                fflush($fp);            // flush output before releasing the lock
                flock($fp, LOCK_UN);    // release the lock
            } else {
                echo "Couldn't get the lock!";
            }

            fclose($fp);

            $response->setStatus(302);
            $response->setHeaders([
                'Location: /dashboard/models'
            ]);
            $response->send();
            exit;
        }

        $models = new \FilesystemIterator(GWM['DIR_ROOT'].'/src/Core/Models');
        $list = '';

        foreach($models as $model) {
            $list .= '<tr><td>' . $model->getBasename('.php') . '</td></tr>';
        }

        $latte->render('themes/admin/templates/models.latte', [
            'list' => $list,
        ]);
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
                $id = $model->Create($schema);

                $response = new \GWM\Core\Response();
                $json = [
                    'success' => true,
                    'data' => [
                        "_id" => $id,
                        "title" => $title,
                        "content" => $content
                    ]
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

            $params = [
                'articles' => $articles
            ];

            $latte = new \Latte\Engine;
            $latte->setTempDirectory('tmp/latte');
            $latte->render('themes/admin/templates/articles.latte', $params);
        }
    }
}