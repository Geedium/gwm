<?php

namespace GWM\Core\Controllers {

    use GWM\Core\{Reader, Schema, Session, Response, Errors\Basic, Template\Engine};

    use GWM\Mapper;

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

        function pingDomain($domain){
            $starttime = microtime(true);
            $file      = fsockopen ($domain, 80, $errno, $errstr, 10);
            $stoptime  = microtime(true);
            $status    = 0;
        
            if (!$file) $status = -1;  // Site is down
            else {
                fclose($file);
                $status = ($stoptime - $starttime) * 1000;
                $status = floor($status);
            }
            return $status;
        }

        public function Projects()
        {
            try {
                $schema = new Schema($_ENV['DB_NAME']);
            } catch (Basic $e) {
                die($e->getMessage());
            }
            Schema::$PRIMARY_SCHEMA = $schema;

            $projects = [
                (object)[
                    'name' => 'Cloth Path',
                    'description' => 'No description.',
                    'leader' => 'trep_trep',
                    'state' => false,
                    'link' => 'clothpath-nextjs-rouge.vercel.app',
                    'private' => true,
                    'ping' => 0,
                    'tags' => [
                        'Cryptocurrency',
                        'Shop'
                    ]
                ]
            ];

            $adverts = [
                (object)[
                    'name' => 'Cherry TEAM',
                    'description' => 'Building Design and Construction Services',
                    'ping' => 0,
                    'active' => false,
                    'link' => 'www.cherryteam.co.uk',
                    'tags' => [
                        'Designing',
                        'Building services'
                    ]
                ]
            ];
            
            $removeAt = [];

            for($i = 0; $i < count($adverts); $i++) {
                if(!$adverts[$i]->active) {
                    $removeAt[] = $i;
                    continue;
                }

                if ($adverts[$i]->link) {
                    $adverts[$i]->ping = $this->pingDomain($adverts[$i]->link);
                }
            }

            foreach($removeAt as $i) {
                \array_splice($adverts, $i);
            }

            if($projects[0]->link) {
                $projects[0]->ping = $this->pingDomain($projects[0]->link);
            }

            if($projects[0]->ping < 0) {
                $projects[0]->ping = 0;
            }

            $response = new Response();
            $html = Engine::Get()->Parse(
                'res/'.$_ENV['FALLBACK_THEME'].'/src/projects.html.latte',
                array_merge(
                    self::ContextChain(),
                    [
                        'username' => Session::Get()->Username(),
                        'projects' => $projects,
                        'adverts' => $adverts
                    ]
                )
            );
            $response->setContent($html)->send();
        }

        public static function ContextChain()
        {
            Session::Get();

            if(!isset(Schema::$PRIMARY_SCHEMA))
            {
                Schema::$PRIMARY_SCHEMA = new Schema($_ENV['DB_NAME']);
            }

            $users = Schema::$PRIMARY_SCHEMA->query("SELECT username FROM `{$_ENV['DB_PREFIX']}_users`")
            ->fetchAll(\PDO::FETCH_CLASS, User::class, []);

            return [
                'user' => Session::Get()->Username(),
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
                ORDER BY pinned DESC, created_at DESC
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
                 //$html = file_get_contents(GWM['DIR_ROOT'].'/res/emberjs.theme.bundle/src/index.html');
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