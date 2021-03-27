<?php

namespace GWM\Core\Controllers {

    use GWM\Core\Errors\Basic;
    use GWM\Core\Models\Article as Model;
    use GWM\Core\Response;
    use GWM\Core\Schema;
    use Latte\Engine;

    /**
     * Class Article
     * @package GWM\Core\Controllers
     * @version 1.0.0
     */
    class Article
    {
        private function Decode($raw_content): string
        {
            $r_html = html_entity_decode($raw_content, ENT_NOQUOTES | ENT_HTML5, 'ISO-8859-1');
            $r_html = preg_replace('~[\r\n]+~', '', $r_html);
            return stripslashes($r_html);
        }

        function Get(array $options = [])
        {
            $id = (int)$options['pux.route'][3]['vars']['id'] ?? 0;
            $slug = (string)$options['pux.route'][3]['vars']['slug'] ?? '';

            try {
                $schema = new Schema($_ENV['DB_NAME']);
                Schema::$PRIMARY_SCHEMA = $schema;
            } catch (Basic $e) {
                die($e->getMessage());
            }

            $model = new Model();

            if($id > 0) {
                $model = $schema->Get(Model::class, $model, [
                    'id' => $id
                ]);
            } else if($slug != '') {

                $stmt = $schema->prepare("SELECT * FROM ${_ENV['DB_PREFIX']}_articles WHERE slug = :slug");
                $stmt->bindParam(':slug', $slug, \PDO::PARAM_STR);
                $stmt->execute();

                $model = $stmt->fetchObject(Article::class);
            }

            if(!$model) exit;

            $model->content = $this->Decode($model->content);

            $response = new Response();

            $html = \GWM\Core\Template\Engine::Get('latte')
                ->Parse("res/${_ENV['FALLBACK_THEME']}/src/core/article.html.latte", 
                    array_merge(\GWM\Core\Controllers\Home::ContextChain(), [
                        'article' => $model
                    ])
                );

            $response->setContent($html)->send(200);
            exit;
        }
    }
}