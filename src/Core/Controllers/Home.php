<?php

namespace GWM\Core\Controllers {

    use GWM\Core\{Reader, Schema, Session, Response, Errors\Basic, Template\Engine};

    use GWM\Core\Models\User;

    use GWM\Core\Utils\ {
        Form,
        Table
    };

    class Home
    {
        private function _substr($str, $start, $length = null)
        {
            return (ini_get('mbstring.func_overload') & 2) ? mb_substr($str, $start, ($length === null) ? mb_strlen($str, '8bit') : $length, '8bit') : substr($str, $start, ($length === null) ? strlen($str) : $length);
        }

        public function Projects()
        {
            try {
                $schema = new Schema($_ENV['DB_NAME']);
            } catch (Basic $e) {
                die($e->getMessage());
            }
            Schema::$PRIMARY_SCHEMA = $schema;

            $response = new Response();
            $html = Engine::Get()->Parse(
                'res/'.$_ENV['FALLBACK_THEME'].'/src/projects.html.latte',
                array_merge(
                    self::ContextChain(),
                    [
                    'username' => Session::Get()->Username()
                ]
                )
            );
            $response->setContent($html)->send();
        }

        public static function ContextChain()
        {
            $users = Schema::$PRIMARY_SCHEMA->query("SELECT username FROM `{$_ENV['DB_PREFIX']}_users`")
            ->fetchAll(\PDO::FETCH_CLASS, User::class, []);

            return [
                'users' => $users
            ];
        }

        public function index(Response $response, int $offset = 0)
        {
            Session::Get();

            // @TODO: Make shorter ver. of Schema (use callables, callbacks?)
            try {
                $schema = new Schema($_ENV['DB_NAME']);
            } catch (Basic $e) {
                die($e->getMessage());
            }
            Schema::$PRIMARY_SCHEMA = $schema;

            $stmt = $schema->prepare("SELECT COUNT(*)
                FROM  ${_ENV['DB_PREFIX']}_articles
            ");

            $stmt->execute();
            
            $total = $stmt->fetchColumn(0);

            $page = $offset;
            $offset *= 5;
            $paginations = ceil($total / 5) - 1;

            if ($page > $paginations) {
                $response->Redirect('/');
            }

            $stmt = $schema->prepare("SELECT * 
                FROM ${_ENV['DB_PREFIX']}_articles
                ORDER BY created_at DESC
                LIMIT 5 
                OFFSET $offset
            ");

            $stmt->execute();

            $articles = $stmt->fetchAll(\PDO::FETCH_CLASS, \GWM\Core\Models\Article::class);

            foreach ($articles as &$article) {
                $article->content = html_entity_decode($article->content, ENT_NOQUOTES | ENT_HTML5, 'ISO-8859-1');
                $article->content = preg_replace('~[\r\n]+~', '', $article->content);
                $article->content = stripslashes($article->content);
                $article->{'slug'} = wordwrap(strtolower($article->title), 1, '-', 0);
            }

            $params = array_merge(self::ContextChain(), [
                'articles' => $articles,
                'paginations' => $paginations,
                'page' => $page,
                'user' => Session::Get()->Username()
            ]);

            try {
                 $html = Engine::Get()->Parse('res/'.$_ENV['FALLBACK_THEME'].'/src/core/index.html.latte', $params);
                 $response->setContent($html)->send();
            } catch (\Exception $e) {
                if (true) {
                    die($e->getMessage());
                }
                $response->Astray();
            }
            
        }
    }
}