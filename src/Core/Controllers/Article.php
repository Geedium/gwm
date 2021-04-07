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

        function PostComment(array $options = [])
        {
            $resp = new Response();

            $id = (int)$options['pux.route'][3]['vars']['id'] ?? 0;
            $slug = (string)$options['pux.route'][3]['vars']['slug'] ?? '';

            $content = htmlspecialchars($_POST['form1']['comment']);

            !\GWM\Core\Session::Get()->Logged() && $resp->Redirect('/');

            try {
                $schema = new Schema($_ENV['DB_NAME']);
            } catch (Basic $e) {
                $resp->Redirect('/');
            }

            strlen($content) <= 0 && $resp->Redirect('/articles/'.$slug);

            $stmt = $schema->prepare("SELECT id 
                FROM ${_ENV['DB_PREFIX']}_articles a
                WHERE a.slug = :slug");
            
            $stmt->execute([':slug' => $slug]);

            $article_id = $stmt->fetchColumn(0);
            if($article_id <= 0) exit; // -- invalid id

            $facade = new \GWM\Core\Facades\User($schema);

            $user = $facade->construct();
            $user_id = (int)$user->id;

            if ($user_id > 0) {
                $stmt = $schema->prepare("INSERT INTO ${_ENV['DB_PREFIX']}_comments
                    (user, article, message) VALUES (:user, :article, :message) ");
                    
                $r = $stmt->execute([
                    ':user' => $user_id,
                    ':article' => $article_id,
                    ':message' => $content
                ]);

                if ($r) {
                    $resp->Redirect('/articles/'.$slug);
                }
            }

            $resp->Redirect('/');
        }

        /**
         * Undocumented function
         *
         * @param array $options
         * @return void
         */
        function Delete(array $options = [])
        {
            !\GWM\Core\Session::Get()->Logged() && exit;
            
            $resp = new Response();
            
            $facade = new \GWM\Core\Facades\User();
            $slug = (string)$options['pux.route'][3]['vars']['slug'] ?? '';
            $comment_id = \filter_input(INPUT_GET, 'comment');

            try {
                $schema = new Schema($_ENV['DB_NAME']);
            } catch (Basic $e) {
                $resp->Redirect('/');
            }

            $user = $facade->construct($schema);
            $auth = new \GWM\Core\Services\Auth($user);

            $stmt = $schema->prepare("SELECT id 
            FROM ${_ENV['DB_PREFIX']}_articles a
            WHERE a.slug = :slug");
        
            $stmt->execute([':slug' => $slug]);

            $article_id = $stmt->fetchColumn(0);
            if($article_id <= 0) exit; // -- invalid id

            
            if($user->id > 0 && $comment_id > 0 && $article_id > 0 
            && $auth->has_role('posts.moderator'))
            {
                $stmt = $schema->prepare("DELETE FROM ${_ENV['DB_PREFIX']}_comments
                WHERE id = :comment AND user = :user AND article = :article");

                $r = $stmt->execute([
                    ':article' => $article_id,
                    ':comment' => $comment_id,
                    ':user' => $user->id
                ]);

                if ($r) {
                    $resp->Redirect('/articles/'.$slug.'/');
                } else {
                    $resp->Astray();
                }
            }

            $resp->Redirect('/articles/'.$slug.'/');
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

            $stmt = $schema->prepare("SELECT c.`id`, c.`message`, u.`username`
                FROM ${_ENV['DB_PREFIX']}_comments c
                LEFT JOIN ${_ENV['DB_PREFIX']}_users u
                ON c.`user` = u.`id`
                LEFT JOIN ${_ENV['DB_PREFIX']}_articles a
                ON c.`article` = a.`id`
                WHERE a.`id` = {$model->id}
            ");

            $stmt->execute();
            
            $comments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $response = new Response();

            $facade = new \GWM\Core\Facades\User($schema);
            $usr = $facade->construct();
            
            $html = \GWM\Core\Template\Engine::Get('latte')
                ->Parse("res/${_ENV['FALLBACK_THEME']}/src/core/article.html.latte", 
                    array_merge(\GWM\Core\Controllers\Home::ContextChain(), [
                        'article' => $model,
                        'comments' => $comments,
                        'usr' => $usr
                    ])
                );

            $response->setContent($html)->send(200);
            exit;
        }
    }
}