<?php

namespace GWM\Core\Controllers {

    use GWM\Core\Annotations;
use GWM\Core\Errors\Basic;
use GWM\Core\Models\Article;
use GWM\Core\Models\User;
use GWM\Core\Services\Auth;
use GWM\Core\Plugin;
use GWM\Core\Utils\Agent;
use GWM\Core\Utils\Debug;
use GWM\Core\Schema;
use GWM\Core\Session;
use GWM\Core\Utils;
use GWM\Core\Response;
use GWM\Core\Router;
use GWM\Core\Utils\Measurement;

    use Latte\Engine;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\PHPMailer;

    use \RobThree\Auth\TwoFactorAuth;

    /**
     * Class Dashboard
     * @package GWM\Core\Controllers
     * @version 1.0.0
     */
    class Dashboard
    {
        /**
         * Entry
         *
         * ❌ GWM - Not passed.
         * 
         * @param Response $response Respond to client with HTTP response
         * @param Auth $auth Authentification middleware
         * @return void
         */
        public function Entry($response = null, $auth = null)
        {
            try {
                Session::Get();

                $has_user = !empty($_SESSION['username']) ?? false;
                $has_password = !empty($_SESSION['password']) ?? false;
                $has_csrf = $_POST['csrf'] ? Session::Get()->Check($_POST['csrf']) : false;

                if (!$has_user and !$has_password) {
                    $schema = new Schema($_ENV['DB_NAME']);
                    $user = new User();
                    $user->_INIT($schema);

                    $username = filter_has_var(INPUT_POST, 'username') ?
                        filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING) : null;

                    $password = filter_has_var(INPUT_POST, 'password') ?
                        filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
                        : null;

                    $user->setUserName($username ?? '');
                    $user->setPassword($password ?? '');

                    $data = $schema->Compare('users', ['username', 'password'], $user->getUsername());

                    if ($has_csrf and password_verify($password, $data[0]['password']) == true) {
                        $_SESSION['username'] = $user->getUserName();
                        $_SESSION['password'] = $user->getPassword();

                        $response = new Response();
                       
                        /* $response->setStatus(301);
                         $response->setHeaders([
                             'Location: /dashboard'
                         ]);*/

                        $html = '';

                        $tfa = new TwoFactorAuth('Geedium');

                        $html .= '<li>First create a secret and associate it with a user';
                        $secret = $tfa->createSecret(160);  // Though the default is an 80 bits secret (for backwards compatibility reasons) we recommend creating 160+ bits secrets (see RFC 4226 - Algorithm Requirements)
                        echo '<li>Next create a QR code and let the user scan it:<br><img src="' . $tfa->getQRCodeImageAsDataUri('My label', $secret) . '"><br>...or display the secret to the user for manual entry: ' . chunk_split($secret, 4, ' ');

                        $code = $tfa->getCode($secret);
                        $html .= '<li>Next, have the user verify the code; at this time the code displayed by a 2FA-app would be: <span style="color:#00c">' . $code . '</span> (but that changes periodically)';
                        $html .= '<li>When the code checks out, 2FA can be / is enabled; store (encrypted?) secret with user and have the user verify a code each time a new session is started.';
                        $html .= '<li>When aforementioned code (' . $code . ') was entered, the result would be: ' . (($tfa->verifyCode($secret, $code) === true) ? '<span style="color:#0c0">OK</span>' : '<span style="color:#c00">FAIL</span>');

                        $response->setContent($html)->send();
                    } else {
                        $google = new \Google_Client();
                        $google->setClientId($_ENV['GOOGLE_CLIENT_ID']);
                        $google->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
                        $google->setRedirectUri('https://www.geedium.com/dashboard');
                        $google->addScope('email');
                        $google->addScope('profile');

                        if (!isset($_SESSION['access_token'])) {
                            $google_auth_url = $google->createAuthUrl();
                        }

                        if (isset($_GET['code'])) {
                            $token = $google->fetchAccessTokenWithAuthCode($_GET['code']);
                            if (!isset($token['error'])) {
                                $google->setAccessToken($token['access_token']);
                                $_SESSION['access_token'] = $token['access_token'];

                                $service = new \Google_Service_Oauth2($google);
                                $data = $service->userinfo->get();

                                $email = $data['email'];
                                $_SESSION['firstname'] = $data['given_name'];
                                $_SESSION['lastname'] = $data['family_name'];

                                if (!empty($email)) {
                                    $schema = new Schema($_ENV['DB_NAME']);
                                    $stmt = $schema->prepare("SELECT username FROM {$_ENV['DB_PREFIX']}_users WHERE email='$email' ");
                                    $stmt->execute();

                                    if ($stmt->rowCount() > 0) {
                                        $username = $stmt->fetchColumn();
                                        $_SESSION['username'] = $username;

                                        $response = new Response();
                                        $response->setHeaders([
                                            'Location: /dashboard'
                                        ]);
                
                                        $response->send(301);
                                        exit;
                                    }
                                }

                                $google->revokeToken();
                                session_destroy();
                            }
                        }

                        $html = \GWM\Core\Template\Engine::Get()->Parse('themes/admin/templates/login.latte', [
                            'csrf' => Session::Get()->generateToken(),
                            'google_url' => $google_auth_url
                        ]);

                        $head = strpos($html, '</head>');
                        $a = substr($html, 0, $head);
                        $b = substr($html, $head, strlen($html));
                        $html = $a . '<script src="/js/gwm.min.js"></script>' . $b;

                        $response = new Response;
                        $response->setStatus(200);
                        $response->setContent($html);
                        $response->send();
                        exit;
                    }
                }
            } catch (\Exception $e) {
                Debug::$log[] = $e;
            } finally {
                $profiler = Router::Profiler();
            }

            /**
             * Service injection is new thing.
             */
            if ($response != null && $auth != null) {
                if ($auth->has_role('admin.dashboard') & true) {
                } else {
                    $html = \GWM\Core\Template\Engine::Get()->Parse(
                        "src/templates/admin/unauthorized.html.latte",
                        [
                        'roles' => [
                            'admin.dashboard'
                        ]
                    ]
                    );
    
                    $response->setContent($html)->send(200);
                    exit;
                }
            } else {
                // BACKWARD COMP.

                $html = \GWM\Core\Template\Engine::Get()->Parse(
                    "src/templates/admin/unauthorized.html.latte",
                    [
                    'roles' => [
                        'admin.dashboard'
                    ]
                ]
                );

                $response = new Response();
                $response->setContent($html)->send(200);
                exit;
            }

            if (!isset(Schema::$PRIMARY_SCHEMA)) {
                try {
                    Schema::$PRIMARY_SCHEMA = new Schema($_ENV['DB_NAME']);
                } catch (Basic $e) {
                    die($e->getMessage());
                }
            }

            

            $users = Schema::$PRIMARY_SCHEMA->Count(User::class);

            $html = \GWM\Core\Template\Engine::Get()->Parse(
                'themes/admin/templates/index.latte',
                array_merge(self::Defaults(), [
                    'users' => $users,
                    'ip' => Agent::ip(),
                    'profiler' => $profiler
                ])
            );

            $head = strpos($html, '</head>');
            $a = substr($html, 0, $head);
            $b = substr($html, $head, strlen($html));
            $html = $a . '<script src="/js/gwm.min.js"></script>' . $b;

            $response = new Response;
            $response->setStatus(200);
            $response->setContent($html);
            $response->send();
            exit;
        }

        public static function Defaults(): array
        {
            $breadcrumb = array_filter(explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
            $breadcrumb_parents = [];

            array_walk($breadcrumb, function (&$value) use (&$breadcrumb_parents) {
                $breadcrumb_parents[] = $value;

                $value = [
                    'name' => $value,
                    'link' => implode('/', $breadcrumb_parents)
                ];
            });

            $tabs = [
                (object)['name' => 'Dashboard', 'icon' => 'tachometer-alt', 'url' => '/dashboard'],
                (object)['name' => 'Analytics', 'icon' => 'chart-bar', 'url' => '/dashboard/analytics'],
                (object)['name' => 'Models', 'icon' => 'bezier-curve', 'url' => '/dashboard/models'],
                (object)['name' => 'Users', 'icon' => 'user', 'url' => '/dashboard/users'],
                (object)['name' => 'Media', 'icon' => 'photo-video', 'url' => '/dashboard/media'],
                (object)['name' => 'Files', 'icon' => 'file', 'url' => '/dashboard/files']
            ];

            Plugin::Ensure();

            return [
                'elapsed' => round(microtime(true) - GWM['START_TIME'], 2),
                'crumbs' => $breadcrumb,
                'username' => $_SESSION['username'] ?? '',
                'avatar' => $_SESSION['username'].'.png' ?? '',
                'firstname' => $_SESSION['firstname'] ?? '',
                'plugins' => Plugin::$plugins,
                'ip' => Agent::ip(),
                'tabs' => $tabs,
                'lastname' => $_SESSION['lastname'] ?? ''
            ];
        }

        public function Logs()
        {
            $response = new Response();

            Session::Get()->Logged() or $response->Astray();

            try {
                $schema = new Schema($_ENV['DB_NAME']);
                $stmt = $schema->prepare("SELECT * FROM {$_ENV['DB_PREFIX']}_logs");
                $stmt->execute();
                $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $html = \GWM\Core\Template\Engine::Get()->Parse(
                    'themes/admin/templates/logs.latte',
                    array_merge(self::Defaults(), [
                        'logs' => $result
                    ])
                );
            } catch (Basic | \Exception $e) {
                $html = $e->getMessage();
            }

            $response->setContent($html)->send();
        }

        public function dir_tree($dir_path)
        {
            $rdi = new \RecursiveDirectoryIterator($dir_path);

            $rii = new \RecursiveIteratorIterator($rdi);

            $tree = [];

            foreach ($rii as $splFileInfo) {
                $file_name = $splFileInfo->getFilename();

                // Skip hidden files and directories.
                if ($file_name[0] === '.') {
                    continue;
                }

                $path = $splFileInfo->isDir() ? array($file_name => array()) : array($file_name);

                for ($depth = $rii->getDepth() - 1; $depth >= 0; $depth--) {
                    $path = array($rii->getSubIterator($depth)->current()->getFilename() => $path);
                }

                $tree = array_merge_recursive($tree, $path);
            }

            return $tree;
        }

        public function Themes()
        {
            $response = new Response();

            Session::Get()->Logged() or $response->Astray();

            $theme = \GWM\Core\Template\Engine::Get()->Parse(
                'themes/admin/templates/basis.latte',
                array_merge(self::Defaults(), [
                    'avatar' => $_SESSION['username'] ?? ''
                ])
            );

            $tree = $this->dir_tree(GWM['DIR_ROOT'].'/resources');

            $key = array_keys($tree)[0];
            $fst = array_values($tree)[0]['templates'];
            $path = GWM['DIR_ROOT'].'/resources/'.$key.'/templates/'.$fst[0];
            $contents = file_get_contents($path);

            $html = \GWM\Core\Template\Engine::Get()->Parse(
                'themes/admin/templates/themes/index.latte',
                array_merge(self::Defaults(), [
                    'themeLoaded' => $theme,
                    'tree' => $tree,
                    'code' => $contents
                ])
            );

            $head = strpos($html, '</head>');
            $a = substr($html, 0, $head);
            $b = substr($html, $head, strlen($html));
            $html = $a . '<script src="/js/gwm.min.js"></script>' . $b;

            $response->setContent($html)->send();
        }

        /**
         * @magic
         */
        public function ThemesA()
        {
            $response = new Response();

            Session::Get()->Logged() or $response->Astray();

            $theme = \GWM\Core\Template\Engine::Get()->Parse(
                'themes/admin/templates/basis.latte',
                array_merge(self::Defaults(), [
                    'avatar' => $_SESSION['username'] ?? ''
                ])
            );

            $head = strpos($theme, '</head>');
            $a = substr($theme, 0, $head);
            $b = substr($theme, $head, strlen($theme));
            $theme = $a . <<<STYLE
<style>
div {
    border: 1px dashed !important;
}
div:hover {
    cursor: grab;
}
</style>
STYLE
 . $b;

            $response->setStatus(200);
            $response->setContent($theme);
            $response->send();
            exit;
        }

        public function Server()
        {
            $response = new Response();

            Session::Get()->Logged() or $response->Astray();

            $r = filter_has_var(INPUT_GET, 'reset');

            if ($r) {
                Utils::delete_dir(GWM['DIR_TMP'].'/gwm');
                $response->Redirect('/dashboard/server');
            }

            $image_size = 0.0;
            $wcms_size = 0.0;
            $tmp_size = 0.0;

            $it = new \RecursiveDirectoryIterator(GWM['DIR_PUBLIC']);
            foreach (new \RecursiveIteratorIterator($it) as $file) {
                $extension = $file->getExtension();

                if ($extension == 'png' or
                    $extension == 'jpeg' or
                    $extension == 'jpg') {
                    $image_size += filesize($file->getPathname());
                }

                unset($extension);
            }

            $it = new \RecursiveDirectoryIterator(GWM['DIR_ROOT'].'/src');
            foreach (new \RecursiveIteratorIterator($it) as $file) {
                $extension = $file->getExtension();
                $wcms_size += filesize($file->getPathname());

                unset($extension);
            }

            $it = new \RecursiveDirectoryIterator(GWM['DIR_ROOT'].'/tmp');
            foreach (new \RecursiveIteratorIterator($it) as $file) {
                $extension = $file->getExtension();
                $tmp_size += filesize($file->getPathname());

                unset($extension);
            }

            $html = \GWM\Core\Template\Engine::Get()->Parse(
                'themes/admin/templates/navbar/server.latte',
                array_merge(self::Defaults(), [
                    'avatar' => strtolower($_SESSION['username']) ?? '',
                    'image_size' => Measurement::Byte($image_size, 2),
                    'wcms_size' => Measurement::Byte($wcms_size, 2),
                    'tmp_size' => Measurement::Byte($tmp_size, 2),
                    'conf' => get_loaded_extensions()
                ])
            );

            $response->setStatus(200);
            $response->setContent($html);
            $response->send();
            exit;
        }

        public function Report()
        {
            $response = new Response();

            Session::Get()->Logged() or $response->Astray();

            if (filter_has_var(INPUT_POST, 'message') == true) {
                $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = $_ENV['MAIL_SMTP_USER'];
                    $mail->Password = $_ENV['MAIL_SMTP_PASS'];
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;

                    $mail->setFrom($_ENV['MAIL_SMTP_USER'], 'geedium.com');
                    $mail->addAddress('gediminas.dausynas@gmail.com');

                    $mail->isHTML(true);
                    $mail->Subject = 'Bug reported';
                    $mail->Body = $message;
                    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                    if ($mail->send()) {
                        $_SESSION['_message'] = "Message sent!";
                        $_SESSION['_success'] = true;
                        $response->Redirect('/dashboard/report');
                    } else {
                        $_SESSION['_message'] = "Message could not be sent.";
                        $_SESSION['_success'] = false;
                        $response->Redirect('/dashboard/report');
                    }
                } catch (Exception $e) {
                    $_SESSION['_message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    $_SESSION['_success'] = false;
                    $response->Redirect('/dashboard/report');
                } catch (\Exception $e) {
                    $_SESSION['_message'] = "Message could not be sent. Error: {$e->getMessage()}";
                    $_SESSION['_success'] = false;
                    $response->Redirect('/dashboard/report');
                }
            }

            $success = false;
            $message = null;

            $_AFTER = isset($_SESSION['_message']);
            if ($_AFTER) {
                $message = $_SESSION['_message'];
                unset($_SESSION['_message']);

                $success = $_SESSION['_success'];
                unset($_SESSION['_success']);
            }

            $latte = new Engine;
            $latte->setTempDirectory('tmp/latte');

            $html = \GWM\Core\Template\Engine::Get()->Parse(
                'themes/admin/templates/report.latte',
                array_merge(self::Defaults(), [
                    'username' => $_SESSION['username'] ?? '',
                    'avatar' => strtolower($_SESSION['username']) ?? '',
                    'message' => $message,
                    'success' => $success,
                    'ip' => Agent::ip(),
                ])
            );

            $response->setContent($html);
            $response->send();
            exit;
        }

        /** @magic */
        public function Dependencies()
        {
            if (!Session::Get()->Logged()) {
                $response = new Response();
                $response->Astray();
            }

            $latte = new Engine;
            $latte->setTempDirectory('tmp/latte');

            $composer = json_decode(file_get_contents('composer.lock'), true)['packages'];

            $html = \GWM\Core\Template\Engine::Get()->Parse(
                'themes/admin/templates/dependencies.latte',
                array_merge(self::Defaults(), [
                    'avatar' => strtolower($_SESSION['username']) ?? '',
                    'vendor' => $composer,
                    'ip' => Agent::ip(),
                ])
            );

            $response = new Response;
            $response->setStatus(200);
            $response->setContent($html);
            $response->send();
            exit;
        }

        /** @magic
         * @throws Basic
         */
        public function Analytics(): void
        {
            if (!Session::Get()->Logged()) {
                $response = new Response();
                $response->Astray();
            }

            $latte = new Engine;
            $latte->setTempDirectory('tmp/latte');

            $schema = new Schema($_ENV['DB_NAME']);

            $users = $schema->Date(User::class, ['YEAR(`created_at`)' => 'YEAR(CURDATE())']);

            $dummy = null;

            list($dummy, // 0
                $january, // 1
                $february, // 2
                $march, // 3
                $april, // 4
                $may, // 5
                $june, // 6
                $july, // 7
                $august, // 8
                $september, // 9
                $october, // 10
                $november, // 11
                $december) = $users; // 12

            unset($dummy);

            $html = \GWM\Core\Template\Engine::Get()->Parse(
                'themes/admin/templates/analytics.latte',
                array_merge(self::Defaults(), [
                    'avatar' => strtolower($_SESSION['username']) ?? '',
                    'users' => implode(',', [
                        $january,
                        $february,
                        $march,
                        $april,
                        $may,
                        $june,
                        $july,
                        $august,
                        $september,
                        $october,
                        $november,
                        $december
                    ]),
                    'ip' => Agent::ip(),
                ])
            );

            $response = new Response;
            $response->setStatus(200);
            $response->setContent($html);
            $response->send();
            exit;
        }

        public function Users()
        {
            $res = new Response();

            !!Session::Get()->Logged() ?: $response->Astray();

            $users = [];

            try {
                $schema = new Schema($_ENV['DB_NAME']);
                $dummy = new User();
                $dummy->_INIT($schema);

                $users = $schema->All(User::class);
            } catch (Basic $e) {
                echo $e->getMessage();
            } catch (\Exception $e) {
                echo $e->getMessage();
            }

            $html = \GWM\Core\Template\Engine::Get()->Parse(
                'themes/admin/templates/users.latte',
                array_merge(self::Defaults(), [
                    'users' =>  $users,
                ])
            );

            $res->setContent($html)->send(Response::HTTP_OK);
        }

        public function Media()
        {
            $response = new Response();

            !!Session::Get()->Logged() ?: $response->Astray();

            if ($_POST['upload_form'] ?? false) {
                if ($_FILES['fileToUpload']['tmp_name'] != null) {
                    // if (substr($_FILES['fileToUpload']['type'], 0, 5) == 'image'
                    // || substr($_FILES['fileToUpload']['type'], 0, 5) == 'video') {
                    move_uploaded_file(
                        $_FILES["fileToUpload"]["tmp_name"],
                        GWM['DIR_PUBLIC'] . '/uploads/' . $_SESSION['username'] .'/'. $_FILES["fileToUpload"]["name"]
                    );
                    // }
                }

                $response->setStatus(301);
                $response->Redirect('/dashboard/media');
            }

            $size = 0;

            $iterator = new \FilesystemIterator(GWM['DIR_PUBLIC'].'/uploads/'.$_SESSION['username'].'/');
            $files = array();
            foreach ($iterator as $info) {
                $len = strlen($info->getExtension()) + 1;
                $_len = strlen($info->getBasename()) - $len;

                $size += filesize($info->getPathname());
                $files[] = [
                    'name' => substr($info->getBasename(), 0, $_len),
                    'ext' => $info->getExtension()
                ];
            }

            $html = \GWM\Core\Template\Engine::Get()->Parse(
                'themes/admin/templates/media.latte',
                array_merge(self::Defaults(), [
                'uploads' => $files,
                'max_sz' => ini_get('upload_max_filesize'),
                'max_post_sz' => ini_get('post_max_size'),
                'user' => $_SESSION['username'],
                'space' => $size,
                'perc' => ($space / 10737418240) * 100
            ])
            );

            $response = new Response;
            $response->setStatus(200);
            $response->setContent($html);
            $response->send();
            exit;
        }

        /** @magic */
        public function Features()
        {
            $response = new Response();

            if (!Session::Get()->Logged()) {
                $response->Astray();
            }

            $routes = file_get_contents(GWM['DIR_ROOT'].'/tmp/gwm/routes.json');

            $html = \GWM\Core\Template\Engine::Get()->Parse(
                'themes/admin/templates/features.latte',
                array_merge(self::Defaults(), [
                    'routes' => json_decode($routes, true) ?? false
                ])
            );

            $response->setContent($html)->send();
        }

        /** @magic */
        public function Settings()
        {
            if (!Session::Get()->Logged()) {
                $response = new Response();
                $response->Astray();
            }

            $latte = new Engine;
            $latte->setTempDirectory('tmp/latte');

            $html = \GWM\Core\Template\Engine::Get()->Parse(
                'themes/admin/templates/settings.latte',
                array_merge(self::Defaults(), [

                ])
            );

            $response = new Response;
            $response->setStatus(200);
            $response->setContent($html);
            $response->send();
            exit;
        }

        public function files()
        {
            $response = new Response();

            if (!Session::Get()->Logged()) {
                $response->Astray();
            }

            try {
                if (Session::Get()->Check($_POST['csrf'])) {
                    if (filter_has_var(INPUT_POST, 'curl-url')) {
                        $url = filter_input(INPUT_POST, 'curl-url');

                        $data = [
                            'url' => urlencode($url)
                        ];

                        $_SESSION['query'] = http_build_query($data);

                        $response->Redirect('/dashboard/files', 301);
                    }
                }
            } catch (\Exception $e) {
                die($e->getMessage());
            }

            $iterator = new \FilesystemIterator(GWM['DIR_ROOT']);
            $files = array();
            foreach ($iterator as $info) {
                $files[] = $info->getBasename();
            }

            $query_data = [];
            parse_str($_SESSION['query'] ?? '', $query_data);
            unset($_SESSION['query']);

            $url = urldecode($query_data['url']);

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_CAINFO, GWM['DIR_ROOT'].'/certs/ca-certificates.crt');
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_KEEP_SENDING_ON_ERROR, 0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $file = curl_exec($ch);

            curl_close($ch);

            unset($query_data);

            $file = nl2br($file);

            // $file = preg_split('/\s+(?![^<>]+>)/m', $file,-1, PREG_SPLIT_DELIM_CAPTURE);

            try {
                $html = \GWM\Core\Template\Engine::Get()->Parse(
                    'themes/admin/templates/files.latte',
                    array_merge(self::Defaults(), [
                        'files' => $files,
                        'lines' => $file,
                        'csrf' => Session::Get()->generateToken()
                    ])
                );
            } catch (\Exception $e) {
                die($e->getMessage());
            }

            http_response_code(200);
            $response->setContent($html);
            $response->send();
            exit;
        }

        /**
         * @magic
         * @param array $options
         * @throws \Exception
         */
        public function ModelsA(array $options = []): void
        {
            $response = new Response();

            if (!Session::Get()->Logged()) {
                $response->Astray();
            }

            $model_name = ucfirst($options['pux.route'][3]['vars']['name']);
            $model_file = file_get_contents("src/Core/Models/$model_name.php");

            if (!$model_file) {
                $response->Astray();
            }

            $a = strpos($model_file, "namespace") + strlen("namespace");

            for ($i = $a; $i < strlen($model_file) - strlen($a); $i++) {
                $b = $model_file[$i];

                if ($b == '{' or $b == ';') {
                    break;
                }
            }

            try {
                $schema = new Schema($_ENV['DB_NAME']);
                Schema::$PRIMARY_SCHEMA = $schema;
            } catch (Basic $e) {
                echo $e->getMessage();
            }

            $namespace = substr($model_file, $a, $i - $a);
            $class = trim($namespace . '\\' . $model_name);
            $class = str_replace(' ', '', $class);
            $data = [];

            try {
                new Annotations($class, $data);
            } catch (\ReflectionException $e) {
                echo $e->getMessage();
            }

            $html = \GWM\Core\Template\Engine::Get()->Parse(
                'themes/admin/templates/models/edit.latte',
                array_merge(self::Defaults(), [
                    'name' => $model_name,
                    'fields' => $data
                ])
            );

            $response->setContent($html)->send();
        }

        public function models(Response $response)
        {
            if (!Session::Get()->Logged()) {
                $response->Astray();
            }

            $action = $_POST['action'] ?? false;

            if ($action) {
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

                $response->Redirect('/dashboard/models', 302);
            }

            $models = new \FilesystemIterator(GWM['DIR_ROOT'] . '/src/Core/Models');

            $_models = [];

            foreach ($models as $model) {
                $_models[] = $model->getBasename('.php');
            }

            $html = \GWM\Core\Template\Engine::Get()->Parse(
                'themes/admin/templates/models/index.latte',
                array_merge(self::Defaults(), [
                    'models' => $_models,
                ])
            );
            $response->setContent($html)->send();
        }

        /**
         * @magic
         * @param array|null $options
         */
        public function deleteArticle(array $options = null)
        {
            $response = new Response();

            if (!Session::Get()->Logged()) {
                $response->Astray();
            }

            try {
                $schema = new Schema($_ENV['DB_NAME']);
            } catch (Basic $e) {
                die($e->getMessage());
            }

            Schema::$PRIMARY_SCHEMA = $schema;

            $id = $options['pux.route'][3]['vars']['id'];
            if ($id) {
                $statement = $schema->prepare("DELETE FROM {$_ENV['DB_PREFIX']}_articles
                    WHERE id=:id
                ");

                $statement->execute([
                    'id' => $id
                ]);

                $response->Redirect('/dashboard/articles');
            }

            $response->Astray();
        }

        public function articles(array $options = null)
        {
            if (!Session::Get()->Logged()) {
                $response = new Response();
                $response->Astray();
            }

            try {
                $schema = new Schema($_ENV['DB_NAME']);
            } catch (Basic $e) {
                die($e->getMessage());
            }

            Schema::$PRIMARY_SCHEMA = $schema;

            if (filter_has_var(INPUT_POST, 'action')) {
                $value = filter_input(INPUT_POST, 'action');

                if ($value == 1) {
                    $_POST['content'] = str_replace(':embed:', '<iframe ', $_POST['content']);
                    $_POST['content'] = str_replace(':embed_end:', '</iframe>', $_POST['content']);

                    $title = filter_input(INPUT_POST, 'title');
                    $id = filter_input(INPUT_POST, 'id');
                    $content = htmlentities($_POST['content'], ENT_QUOTES | ENT_HTML5, false);

                    $statement = $schema->prepare("UPDATE {$_ENV['DB_PREFIX']}_articles
                        SET title=:title, content=:content
                        WHERE id=:id
                    ");

                    if (!$statement) {
                        die(print_r($statement->errorInfo()));
                    }

                    if (!$statement->execute([
                        'title' => $title,
                        'id' => $id,
                        'content' => $content
                    ])) {
                        die(print_r($statement->errorInfo()));
                    }

                    $response = new Response();
                    $response->Redirect('/dashboard/articles');
                }
            }

            if (\filter_has_var(INPUT_POST, 'title')
                && \filter_has_var(INPUT_POST, 'content')
                && !\filter_has_var(INPUT_POST, 'id')) {
                try {
                    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
                    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);

                    $schema = new Schema($_ENV['DB_NAME']);
                    Schema::$PRIMARY_SCHEMA = $schema;

                    $model = new Article();
                    $model->setTitle($title);
                    $model->setContent($content);
                    $id = $model->Create($schema);

                    $response = new Response();
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
                    \trigger_error($e->getMessage(), E_USER_ERROR);
                }
            } elseif (\filter_has_var(INPUT_POST, 'id')) {
                try {
                    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
                    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
                    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

                    $schema = new Schema($_ENV['DB_NAME']);
                    Schema::$PRIMARY_SCHEMA = $schema;

                    $model = new Article();
                    $model->setTitle($title);
                    $model->setContent($content);
                    $model->Save($schema, $id);

                    $response = new Response();
                    $response->setContent('')->send(201);
                } catch (\Exception $e) {
                    \trigger_error($e->getMessage(), E_USER_ERROR);
                }
            } else {
                try {
                    $schema = new Schema($_ENV['DB_NAME']);
                    Schema::$PRIMARY_SCHEMA = $schema;

                    $model = new Article();
                    $articles = $model->Select($schema);

                    $id = $options['pux.route'][3]['vars']['id'];
                    if ($id) {
                        $model = $schema->Get(Article::class, $model, [
                            'id' => $id
                        ]);
                    } else {
                        $model = null;
                    }

                    $latte = new Engine;
                    $latte->setTempDirectory('tmp/latte');
                    $latte->render(
                        'themes/admin/templates/articles.latte',
                        array_merge(self::Defaults(), [
                            'articles' => $articles,
                            'model' => $model,
                        ])
                    );
                } catch (Basic $e) {
                    echo $e->getMessage();
                }
            }

            exit;
        }
    }
}
