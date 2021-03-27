<?php

namespace GWM\Forum\Controllers {

    use GWM\Forum\Models\Category;
    use GWM\Forum\Models\Discussion;
    use GWM\Core\{Response, Schema, Session, Utils\Table};

    use GWM\Core\Template\Engine;

    /**
     * Class Dashboard
     *
     * Dashboard is the page that shows the
     * analysis of the application's data,
     * trends, summaries.
     *
     * @package GWM\Forum\Controllers
     * @version 1.0.0
     */
    class Dashboard
    {
        function Discussions()
        {
            $response = new Response();

            Session::Get()->Logged() or $response->Astray();

            $table = new Table();
            $table->Hint(Discussion::class);

            try {
                $html = Engine::Get('latte')->Parse('themes/admin/templates/discussions.latte',
                    array_merge(\GWM\Core\Controllers\Dashboard::Defaults(), [
                        'table' => $table
                    ])
                );
            } catch (\Exception $e) {
                $html = $e->getMessage();
            }

            $response->setContent($html)->send();
        }

        function CRUD(array $options)
        {
            $response = new Response();

            Session::Get()->Logged() or $response->Astray();

            $method = filter_var($options['pux.route'][3]['vars'][1], 513);

            $html = 'Method not recognized.';

            try {
                $schema = new Schema($_ENV['DB_NAME']);
                Schema::$PRIMARY_SCHEMA = $schema;
            } catch (\Exception $e) {
                die($e->getMessage());
            }

            switch ($method) {
                case 'add':
                    $table = new Table();
                    $table->Hint(Category::class);
                    $table->SET_CRUD('CREATE');

                    $l = strripos($_SERVER['REQUEST_URI'], '/');
                    $cut = substr($_SERVER['REQUEST_URI'], 0, $l);

                    $table->Handle($response, $schema, $cut, 'b61253f2a0_categories');

                    $html = Engine::Get('latte')->Parse(
                        'themes/admin/templates/snippet_table.latte',
                        array_merge(\GWM\Core\Controllers\Dashboard::Defaults(), [
                            'table' => $table,
                            'title' => 'Add Category'
                        ])
                    );
                    break;
                case 'update':
                    $table = new Table();
                    $table->Hint(Category::class);
                    $table->SET_CRUD('CREATE');

                    $html = Engine::Get('latte')->Parse(
                        'themes/admin/templates/snippet_table.latte',
                        array_merge(\GWM\Core\Controllers\Dashboard::Defaults(), [
                            'table' => $table,
                            'title' => 'Update Category'
                        ])
                    );
                    break;
            }

            $response->setContent($html);
            $response->send();    
        }

        function Topics()
        {
            $response = new Response();

            Session::Get()->Logged() or $response->Astray();

            try {
                $schema = new Schema($_ENV['DB_NAME']);
                Schema::$PRIMARY_SCHEMA = $schema;

                $topics = $schema->Select('b61253f2a0_topics');

                $html = Engine::Get('latte')->Parse('themes/admin/templates/topics.latte',
                    array_merge(\GWM\Core\Controllers\Dashboard::Defaults(), [
                        'topics' => $topics
                    ])
                );
            } catch (\Exception $e) {
                $html = $e->getMessage();
            }

            $response->setContent($html);
            $response->send();
            exit(0);
        }

        function Categories(Response $response)
        {
            Session::Get()->Logged() or $response->Astray();

            try {
                $schema = new Schema($_ENV['DB_NAME']);
                Schema::$PRIMARY_SCHEMA = $schema;

                $categories = $schema->Select('b61253f2a0_categories');

                $table = new Table();
                $table->Hint(Category::class);
                $table->All($categories);
                $table->Handle($response, $schema, '/dashboard/forum/categories');

                $html = Engine::Get('latte')->Parse('themes/admin/templates/forum/categories.latte',
                    array_merge(\GWM\Core\Controllers\Dashboard::Defaults(), [
                        'table' => $table,
                    ])
                );
            } catch (\Exception $e) {
                $html = $e->getMessage();
            }

            $response->setContent($html)->send();
        }
    }
}
