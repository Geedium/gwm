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
        // function getCategories(PDO $db, $parentId = null)
        // {
        //     $sql = $parentId ? 'WITH RECURSIVE cte (id, name, parent_id) AS (SELECT id, name, parent_id FROM categories WHERE parent_id = ? UNION ALL SELECT c.id, c.name, c.parent_id FROM categories c INNER JOIN cte ON c.parent_id = cte.id) SELECT * FROM cte' : 'SELECT * FROM categories';
        //     $stmt = $db->prepare($sql);
        //     $stmt->execute($parentId ? [$parentId] : null);
        //     return $stmt->fetchAll();
        // }

        function formCategories($categories, $parentId = null) {
            $categoryList = [];
            $category = [];

            if($parentId == null) {
                $category = array_filter($categories, function($e) {
                    return $e->parent_id == null;
                });
            } else {
                $category = array_filter($categories, function($e) use($parentId) {
                    return $e->parent_id == $parentId;
                });
            }

            foreach ($category as $cat) {
                array_push($categoryList, [
                    'id' => $cat->id,
                    'title' => $cat->title,
                    'children' => $this->formCategories($categories, $cat->id)
                ]);
            }

            return $categoryList;
        }

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

            $categories = $this->formCategories($categories);

            $response->setContent(Engine::Get()
                ->Parse("res/{$_ENV['FALLBACK_THEME']}/src/forum/index.html.latte", [
                    'topics' => $topics,
                    'categories' => $categories
                ])
            )->send(200);

        }
    }
}