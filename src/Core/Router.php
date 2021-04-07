<?php

namespace GWM\Core;

use GWM\Commerce\Controllers\Paysera;
use GWM\Commerce\Controllers\Store;
use GWM\Core\Controllers\Article;
use GWM\Core\Controllers\Dashboard;
use GWM\Core\Controllers\Mail;
use GWM\Core\Models\User;
use GWM\Core\Utils\Debug;

use GWM\Forum\Models\Category;
use Pux\Mux;
use Pux\RouteExecutor;

/**
 * Undocumented class
 */
class Router
{
    protected static array $routes = [];
    private static array $req = [];
    private $url;

    //public static Mux $_ROUTER;

    /**
     * @magic
     */
    function __construct()
    {
        self::$req = [
            'url' => rtrim($_SERVER['REQUEST_URI'], '/'),
            'method' => $_SERVER['REQUEST_METHOD']
        ];

        $this->url = filter_var(rtrim($_SERVER['REQUEST_URI'], '/'), FILTER_SANITIZE_URL);

        //$json = \file_get_contents('routes.json');
        //$data = \json_decode($json);

        // $this->routes = $data;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public static function Route()
    {
        foreach (self::routes as $route) {
            $pattern = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($route['url'])) . "$@D";
            $matches = [];

            if (self::$req['method'] == $route['method'] && preg_match($pattern, self::$req['url'], $matches)) {
                array_shift($matches);
                return call_user_func_array($route['callback'], $matches);
            }
        }
    }

    function Compile()
    {
        unlink(GWM['DIR_ROOT'] . '/tmp/gwm/routes.json');
        unlink(GWM['DIR_ROOT'] . '/tmp/gwm/routes.cfg.json');

        $final_routes = json_decode(file_get_contents(GWM['DIR_ROOT'] . '/config/routes/core.json'), true);
        if (!$final_routes) die(debug_backtrace());

        $di = new \DirectoryIterator(GWM['DIR_ROOT'] . '/src');
        foreach ($di as $info) {
            $name = $info->getBasename();

            if ($name == '.' || $name == '..' || $name == "Core")
                continue;

            $filename = strtolower($name);
            
            $routes = json_decode(file_get_contents(GWM['DIR_ROOT'] . "/config/routes/$filename.json"), true);
            foreach ($routes as $method => $route) {
                $final_routes[$method] ??= [];

                foreach ($route as $slug => $props) {
                    $props[0] = 'GWM\\' . $name . '\\Controllers\\' . $props[0];
                    $final_routes[$method][$slug] = $props;
                }
            }
        }

        $final_routes = json_encode($final_routes);

        file_put_contents(GWM['DIR_ROOT'] . '/tmp/gwm/routes.json', $final_routes);

        file_put_contents(GWM['DIR_ROOT'] . '/tmp/gwm/routes.cfg.json', json_encode([
            'modified' => filemtime(GWM['DIR_ROOT'] . '/tmp/gwm/routes.json')
        ]));
    }

    function getMimeType(string $ext): string
    {
        switch($ext)
        {
            case 'png': return 'image/png'; // 	Portable Network Graphics
            case 'zip': return 'application/zip'; // ZIP archive
            case 'rar': return 'application/vnd.rar'; // RAR archive
            case 'mp4': return 'video/mp4'; // MPEG-4
            case 'jpg': return 'image/jpeg'; // JPEG images
            case 'jpeg': return 'image/jpeg'; // JPEG images
        }

        return 'application/octet-stream';
    }

    function Resolve(Response $response)
    {
        $security_middleware = new \GWM\Core\Middlewares\SecurityMiddleware();

        $request = new Request();
        $request->add_middleware($security_middleware);

        //self::$_ROUTER = $mux = new Mux;
        $mux = new Mux;

        if (!is_dir(GWM['DIR_ROOT'].'/tmp')) {
            mkdir(GWM['DIR_ROOT'] . '/tmp');
        }

        if (!is_dir(GWM['DIR_ROOT'].'/tmp/gwm')) {
            mkdir(GWM['DIR_ROOT'] . '/tmp/gwm');
        }

        if (!file_exists(GWM['DIR_ROOT'].'/tmp/gwm/routes.json')) {
            $this->Compile();
        } else {
            $mod = file_get_contents(GWM['DIR_ROOT'] . '/tmp/gwm/routes.cfg.json');
            $mod = json_decode($mod)->modified;
            if (filemtime(GWM['DIR_ROOT'] . '/tmp/gwm/routes.json') > $mod) {
                $this->Compile();
            }
        }

        $routes = file_get_contents(GWM['DIR_ROOT'].'/tmp/gwm/routes.json');
        $routes = json_decode($routes);

        foreach ($routes as $method => $route) {
            foreach ($route as $slug => $props) {
                $controller = $props[0];
                $action = $props[1];

                if ($slug == '/') {
                    $slug = '';
                }

                $mux->$method($slug, function () use ($controller, $action, $response) {
                    $app = new $controller();
                    $app->$action($response);
                });
            }
        }

        $this->match_user_profile(function($response, $username) {
            $user_controller = new \GWM\Core\Controllers\User();
            $user_controller->Profile($response, $username);
            exit;
        }, $response);

        /*
        $this->Match('/send', function() {
            $name = "NP";
            $message = trim(htmlspecialchars($_POST['message']));

            if(!$message)
                die;

            $time = time();

            $schema = new Schema($_ENV['DB_NAME']);
            $stmt = $schema->prepare("INSERT INTO {$_ENV['DB_PREFIX']}_chat (name, time, message) VALUES (?, ?, ?)");
            $stmt->bindParam(1, $name, \PDO::PARAM_STR);
            $stmt->bindParam(2, $time, \PDO::PARAM_INT);
            $stmt->bindParam(3, $message, \PDO::PARAM_STR);
            $stmt->execute();

            $ch = curl_init("http://localhost:2557");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

            // we send JSON encoded data to the Node.JS server
            $jsonData = json_encode([
                'name' => $name,
                'message' => $message
            ]);

            echo $jsonData;

            $query = http_build_query(['data' => $jsonData]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            exit;
        });

        $this->Match('/no-image', function() {
            $path = GWM['DIR_PUBLIC'].'/images/no-image.png';
            $cmp_dir = GWM['DIR_PUBLIC'].'/images/no-image-compressed.png';

            if (!\file_exists($cmp_dir)) {
                \GWM\Core\Utils\Image::compress($path, $cmp_dir, 50);
            }
            
            $response = new Response();
            $response->setContent(\file_get_contents($cmp_dir));
            $response->send(200);

            exit;
        });

        $this->Match('/api/articles', function () {
            Session::Get();

            try {
                $schema = new Schema($_ENV['DB_NAME']);
            } catch (Basic $e) {
                die($e->getMessage());
            }
            Schema::$PRIMARY_SCHEMA = $schema;

            $model = new \GWM\Core\Models\Article();
            $articles = $model->Select($schema, 'created_at DESC');

            $a = [];

            foreach ($articles as $article) {
                $content = html_entity_decode($article['content'], ENT_NOQUOTES | ENT_HTML5, 'ISO-8859-1');
                $content = preg_replace('~[\r\n]+~', '', $content);
                $content = stripslashes($content);

                $a[] = [
                    'title' => $article['title'],
                    'posted' => $article['created_at'],
                    'content' => $content,
                ];
            }

            $response = new Response();
            $response->sendJson($a);

            exit;
        });

        $this->Match('/dashboard/build', function () {
            die(new Distributor('admin'));
        });

        $this->Match('/dashboard/media', function () {
            $dash = new Controllers\Dashboard();
            $dash->media();
            $this->Profiler();
            exit;
        });*/

        $mux->get('/:id', function(array $options = []) {
            $object = new \GWM\Core\Controllers\Home();
            $object->index(new \GWM\Core\Response(), $options['pux.route'][3]['vars']['id']);
        } , [
            'require' => [ 'id' => '\d+', ],
            'default' => [ 'id' => '1', ]
        ]);
        
        $mux->get('/articles/:slug?comment=:id&action=delete', [\GWM\Core\Controllers\Article::class, 'Delete']);

        $mux->get('/articles/:slug', [\GWM\Core\Controllers\Article::class, 'Get']);
        $mux->post('/articles/:slug', [\GWM\Core\Controllers\Article::class, 'PostComment']);

        $mux->post('/checkout', function() use($response) {
            $controller = new \GWM\Commerce\Controllers\Checkout();
            $controller->create_order($response);
            exit;
        });

        $mux->get('/paypal/result?:query', [\GWM\Commerce\Controllers\Paypal::class, 'accept']);

        $mux->get('/dist/:user/:file/:ext', function($options) use ($response) {

            $user = $options['pux.route'][3]['vars']['user'];
            $file = urldecode($options['pux.route'][3]['vars']['file']);
            $ext = $options['pux.route'][3]['vars']['ext'];

            $filename = GWM['DIR_PUBLIC']."/uploads/$user/$file.$ext";

            if(file_exists($filename))
            {
                ob_get_clean();

                if($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg')
                {
                    header("Content-Disposition: inline");
                } else {
                    header('Content-Description: File Transfer');
                    header("Content-Disposition: attachment; filename=$file.$ext");
                    header('Content-Transfer-Encoding: binary');
                }
                header('Content-Type: '.$this->getMimeType($ext));
                header_remove('x-powered-by');

                ob_clean();
                flush();

                readfile($filename);
                exit;
            }
            else {
                die(http_response_code(500));
            }
        });

        $mux->get('/dashboard/store', function () use ($response) {
            $response->Redirect('/dashboard/store/products', 301);
        });

        $mux->get('/dashboard/analytics', [
            \GWM\Core\Controllers\Dashboard::class,
            'Analytics'
        ]);

        $mux->get('/dashboard/store/manufacturers', [
            \GWM\Commerce\Controllers\Store::class,
            'Manufacturers'
        ]);

        $mux->get('/dashboard/dependencies', [
            \GWM\Core\Controllers\Dashboard::class,
            'Dependencies'
        ]);

        $mux->get('/dashboard/media', [
            \GWM\Core\Controllers\Dashboard::class,
            'Media'
        ]);

        $mux->get('/dashboard/features', [
            \GWM\Core\Controllers\Dashboard::class,
            'Features'
        ]);

        $mux->get('/dashboard/server', [Dashboard::class, 'Server']);
        $mux->get('/dashboard/server?:query', [Dashboard::class, 'Server']);

        $mux->get('/dashboard/settings', [
            \GWM\Core\Controllers\Dashboard::class,
            'Settings'
        ]);

        $mux->post('/dashboard/media', [
            \GWM\Core\Controllers\Dashboard::class,
            'Media'
        ]);

        /**
         * Service Injection
         */

        $mux->get('/dashboard', function() {
            $manifest = [
                [
                    'service' => \GWM\Core\Services\Auth::class,
                    'factory' => [
                        [
                            \GWM\Core\Facades\User::class,
                        ]
                    ]
                ],
            ];

            $services = array();

            foreach ($manifest as $svm) {
                $args = array();

                for ($n = 0; $n < sizeof($svm['factory']); $n++) {
                    $facade = new $svm['factory'][$n][0]();
                    $args[] = $facade->construct();
                }

                $services[] = new $svm['service'](...$args);
            }

            $dashboard = new \GWM\Core\Controllers\Dashboard();
            $dashboard->Entry(new Response(), ...$services);
        });

        $mux->post('/dashboard', [
            \GWM\Core\Controllers\Dashboard::class,
            'Entry'
        ]);

        $mux->get('/dashboard?:query', [
            \GWM\Core\Controllers\Dashboard::class,
            'Entry'
        ]);

        /**
         * End
         */

        $mux->get('/dashboard/store/products', [
            \GWM\Commerce\Controllers\Store::class,
            'Products'
        ]);

        /*
        $this->Match('/sign-out?scope=dashboard', function () {
            $auth = new \GWM\Core\Controllers\Auth();
            $auth->Logout();
        });

        $this->Match('/sign-out', function () {
            $auth = new \GWM\Core\Controllers\Auth();
            $auth->Logout();
        });
        */

        $mux->get('/forum', [\GWM\Forum\Controllers\Home::class, 'index']);

        $mux->get('/dist/png/:png', [\GWM\Core\Controllers\Cache::class, 'png']);

        $mux->get('/auth', [ Controllers\Auth::class, 'index']);
        $mux->get('/auth?:query', [ Controllers\Auth::class, 'index']);
        $mux->post('/auth', [Controllers\Auth::class, 'login']);

        $mux->get('/dashboard/files',
            [
                Controllers\Dashboard::class,
                'files'
            ]);

        $mux->post('/dashboard/files',
            [
                Controllers\Dashboard::class,
                'files'
            ]);

        /**
         * DASHBOARD
         */
        $mux->get('/dashboard/report', [Dashboard::class, 'Report']);
        $mux->post('/dashboard/report', [Dashboard::class, 'Report']);

        $mux->get('/dashboard/models/edit/:name', [Dashboard::class, 'ModelsA'], [
            'require' => ['name' => '[a-z^/]+']
        ]);

        $mux->get('/dashboard/load/theme/0', [Dashboard::class, 'ThemesA']);

        $mux->get('/dashboard/themes', [Dashboard::class, 'Themes']);

        $mux->get('/dashboard/articles/edit/:id', [Dashboard::class, 'articles'], [
            'require' => ['id' => '\d+',],
            'default' => ['id' => '1',]
        ]);

        $mux->get('/dashboard/articles/del/:id', [Dashboard::class, 'deleteArticle'], [
            'require' => ['id' => '\d+',],
            'default' => ['id' => '1',]
        ]);

        $mux->post('/dashboard/articles/edit/:id', [Dashboard::class, 'articles'], [
            'require' => ['id' => '\d+',],
            'default' => ['id' => '1',]
        ]);

        $mux->any('/dashboard/forum/categories/:crud', [\GWM\Forum\Controllers\Dashboard::class, 'CRUD']);

        /**
         * CORE
         */
        $mux->get('/article/:id', [Article::class, 'Get'], [
            'require' => ['id' => '\d+',],
            'default' => ['id' => '1',]
        ]);

        $mux->get('/orders', [\GWM\Commerce\Controllers\OrdersController::class, 'listAction']);

        /**
         * COMMERCE BEGIN:
         */
        $mux->get('/store/add/:id', [Store::class, 'Add'], [
            'require' => ['id' => '\d+',],
            'default' => ['id' => '1',]
        ]);

        $mux->post('/dashboard/store/products/edit/:id', [\GWM\Commerce\Controllers\Dashboard::class, 'Edit'], [
            'require' => ['id' => '\d+',],
            'default' => ['id' => '1',]
        ]);

        $mux->get('/dashboard/store/products/delete/:id', [\GWM\Features\Commerce\Controllers\Dashboard::class, 'Delete'], [
            'require' => ['id' => '\d+',],
            'default' => ['id' => '1',]
        ]);

        $mux->get('/dashboard/store/products/edit/:id', [\GWM\Commerce\Controllers\Dashboard::class, 'Edit'], [
            'require' => ['id' => '\d+',],
            'default' => ['id' => '1',]
        ]);
        /**
         * COMMERCE END;
         */

        $mux->get('/mail/send', [Mail::class, 'Send'], ['type' => 1]);

        $mux->get('/dashboard/articles',
            [
                Controllers\Dashboard::class,
                'articles'
            ]);

        $route = $mux->dispatch($this->url);

        if ($route) {
            \GWM\Core\App::finalize_request($request);

            $response = RouteExecutor::execute($route);
        } else {
            $html = \GWM\Core\Template\Engine::Get()
                ->Parse('src/templates/http/404.html.latte');
            $response->setContent($html)->send(404);
        }
    }

    public static function Profiler()
    {
        $latte = new \Latte\Engine;
        $latte->setTempDirectory('tmp/latte');
        return $latte->renderToString('themes/admin/templates/_profiler.latte', [
            'exceptions' => Debug::$log,
            'elapsed' => round(microtime(true) - GWM['START_TIME'], 2)
        ]);
    }

    function match_user_profile($function, $response)
    {
        $url = $_SERVER['REQUEST_URI'];
        $regex = '#^/profile/(\w+)$#';
        $matches=array();

        preg_match($regex, $url, $matches);

        if ($matches[1]) {
            $user = filter_var($matches[1], FILTER_SANITIZE_URL);
        }

        if(!$user) return false;

        $length = strlen($user);

        if ($length > 0) {
            $delimiters = strpos($user, '/');
            if (!$delimiters) {
                $function->__invoke($response, $user);
            }
        }
        
        return false;
    }

    function Match($url, $function)
    {
        if ($_SERVER['REQUEST_URI'] == $url) {
            $function->__invoke();
        }
    }
}