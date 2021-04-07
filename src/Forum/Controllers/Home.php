<?php

namespace GWM\Forum\Controllers {

    use GWM\Forum\Models\Category;
    use GWM\Forum\Models\Topic;
    use GWM\Core\{App, Response, Router, Schema, Session, Template\Engine};

    /**
     * Class Home
     * @package GWM\Forum\Controllers
     */
    class Home
    {
        function index()
        {
            $response = new Response();

            $hex = App::Hash(0, 'Forum');

            $schema = new Schema($_ENV['DB_NAME']);
            $schema->Create(Topic::class, $hex . '_topics');
            $schema->Create(Category::class, $hex.'_categories');

            $topics = $schema->All([
                $hex . '_topics',
                Topic::class
            ]);

            $categories = $schema->All([
                $hex. '_categories',
                Category::class
            ]);

            $response->setContent(Engine::Get()
                ->Parse("res/{$_ENV['FALLBACK_THEME']}/src/forum/index.html.latte", [
                    'topics' => $topics,
                    'categories' => $categories
                ])
            )->send(200);

        }
    }
}