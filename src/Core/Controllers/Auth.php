<?php

namespace GWM\Core\Controllers {

    use Exception;

    use GWM\Core\{Interfaces\IAuth, Response, Schema, Session, Singleton, Template\Engine as Preprocessor};
    use GWM\Core\Models\ {User};
    use GWM\Core\Exceptions\ {Basic};

    use function filter_has_var;
    use function filter_input;

    /**
     * Class Auth
     * @internal
     * @package GWM\Core\Controllers
     * @version 1.0.0
     */
    class Auth implements IAuth
    {
        public function Logout()
        {
            $scope = $_GET['scope'] ? '/' . $_GET['scope'] : '/';

            Session::Get()->Logout();

            $response = new Response();
            $response->setStatus(301)->setHeaders(["Location: $scope"])->send();
            exit;
        }

        public function index()
        {
            $response = new Response();

            if (Session::Get()->Logged()) {
                $response->Redirect('/');
            }

            try {
                $google = new \Google_Client();
                $google->setClientId($_ENV['GOOGLE_CLIENT_ID']);
                $google->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
                $google->setRedirectUri('https://www.geedium.com/auth');
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
                        $firstname = $data['given_name'];
                        $lastname = $data['family_name'];
                        
                        $_SESSION['firstname'] = $firstname;
                        $_SESSION['lastname'] = $lastname;

                        if (!empty($email)) {
                            $schema = new Schema($_ENV['DB_NAME']);
                            $stmt = $schema->prepare("SELECT username FROM {$_ENV['DB_PREFIX']}_users WHERE email='$email' ");
                            $stmt->execute();

                            if ($stmt->rowCount() > 0) {
                                $username = $stmt->fetchColumn();
                                
                                $jwt_token = \Firebase\JWT\JWT::encode([
                                    'username' => $username
                                ], $_ENV['JWT_KEY'], 'HS256');
                                
                                $stmt = $schema->prepare("UPDATE ${_ENV['DB_PREFIX']}_users
                                SET token = ? WHERE username= ? ");
    
                                $stmt->execute([
                                    $jwt_token,
                                    $username
                                ]);

                                /** @todo Find a better way to do this. */
                                setcookie("JWT_TOKEN", $jwt_token, 
                                    time()+3600, '', 
                                    '.geedium.com',
                                    true,
                                    true
                                );
                                 
                                $_SESSION['username'] = $username;

                                $response->setHeaders([
                                    'Location: /'
                                ]);
        
                                $response->send(301);
                                exit;
                            } else {
                                $user = new User();
                                $user->_INIT($schema);
                                
                                $jwt_token = \Firebase\JWT\JWT::encode([
                                    'username' => $email
                                ], $_ENV['JWT_KEY'], 'HS256');

                                $stmt = $schema->prepare("UPDATE ${_ENV['DB_PREFIX']}_users
                                SET token = ? WHERE username= ? ");
    
                                $stmt->execute([
                                    $jwt_token,
                                    $username
                                ]);
    
                                setcookie("JWT_TOKEN", $jwt_token, 
                                    time()+3600, '', 
                                    \GWM\Core\App::DEBUG_MODE ? '127.0.0.1':'.geedium.com',
                                    true,
                                    true
                                 );

                                $user->setUserName($email);
                                $user->setPassword("");
                                $user->setEmail($email);
                                $user->setFirstname($firstname);
                                $user->setLastname($lastname);
                                $user->token = $jwt_token;

                                $user->register($schema);

                                $_SESSION['username'] = $email;

                                $response->setHeaders([
                                    'Location: /'
                                ]);
        
                                $response->send(301);

                                exit;
                            }
                        }

                        $google->revokeToken();
                        session_destroy();
                    }
                }

                $response->setContent(Preprocessor::Get()->Parse(
                    'res/geedium.theme.bundle/src/core/login.html.latte',
                [
                    'csrf' => Session::Get()->generateToken(),
                    'google_url' => $google_auth_url
                ]
                ))->send();
            } catch (\Exception $e) {
                die($e->getMessage());
            }
        }

        public function login()
        {
            $response = new Response();

            if(Session::Get()->Logged()) {
                $response->Redirect('/');
            }

            try {
                if (Session::Get()->Check($_POST['csrf']) == true) {
                    $this->POST(function ($schema, $model) use($response) {
                        $data = $schema->Compare('users', ['username', 'password'], $model->getUsername());

                        // CSRF TOKEN is valid create JWT.
                        if(password_verify($_POST['password'], $data[0]['password']) == true) {
                            $_SESSION['username'] = $_POST['username'];
                            
                            $jwt_token = \Firebase\JWT\JWT::encode([
                                'username' => $_SESSION['username']
                            ], $_ENV['JWT_KEY'], 'HS256');
                            
                            $stmt = $schema->prepare("UPDATE ${_ENV['DB_PREFIX']}_users
                            SET token = ? WHERE username= ? ");

                            $stmt->execute([
                                $jwt_token,
                                $_SESSION['username']
                            ]);

                            setcookie("JWT_TOKEN", $jwt_token, 
                                time()+3600, '', 
                                \GWM\Core\App::DEBUG_MODE ? '127.0.0.1':'.geedium.com',
                                true,
                                true
                             );

                            $response->setHeaders([
                                'Location: /'
                            ]);

                            $response->send(301);
                        } else {
                            $response->Redirect('/');
                        }

                        exit;
                    });
                }
            } catch (\Exception $e) {
                die($e->getMessage());
            }
        }

        private function POST(\Closure $closure)
        {
            try {
                $schema = new Schema($_ENV['DB_NAME']);
                $model = new User();
                $model->_INIT($schema);

                $func = call_user_func(function () use ($schema, $closure, $model) {
                    if (!filter_has_var(INPUT_POST, 'username') == true
                        or !filter_has_var(INPUT_POST, 'password') == true) {
                        return false;
                    }

                    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING, FILTER_REQUIRE_SCALAR);
                    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING, FILTER_REQUIRE_SCALAR);
                    $username = trim($username);
                    $password = trim($password);

                    if (empty($username) or empty($password) == true) {
                        return false;
                    }

                    $model->setUserName($username)->setPassword($password);

                    call_user_func($closure, $schema, $model);
                    return true;
                });

                if (!$func) {
                    throw new \Exception('Invalid GET or POST!');
                }
            } catch (Basic $e) {
                $response = new Response();
                $response->setContent($e->getMessage());
                $response->sendJson(503);
            } catch (\Exception $e) {
                $response = new Response();
                $response->setContent($e->getMessage());
                $response->sendJson(500);
            }
        }
    }
}