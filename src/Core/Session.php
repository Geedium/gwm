<?php

namespace GWM\Core {

    /**
     * Class Session
     * @internal
     * @package GWM\Core
     * @version 1.0.0
     */
    class Session extends Singleton
    {
        private array $config;

        /**
         * Session destructor.
         * @magic
         */
        function __destruct()
        {
            
        }

        function Logout()
        {
            if (session_status() == PHP_SESSION_ACTIVE)
            {
                session_unset();
                session_destroy();
            }

                // Set cookie expired.
            setcookie("JWT_TOKEN", false, 
                time()-3600, '', 
                \GWM\Core\App::DEBUG_MODE ? '127.0.0.1':'.geedium.com',
                true,
                true);
        }

        /**
         * Undocumented function
         *
         * @return string
         * @throws \Exception
         */
        public function generateToken(): string
        {
            if ($this->config['auth-type'] == 'jwt') {
                
            }

            if (empty($_SESSION['token']) == true) {
                $_SESSION['token'] = bin2hex(random_bytes(32));
            }

            return hash_hmac('sha256', 'GWM_AUTH', $_SESSION['token']);
        }

        /**
         * Function Check
         *
         * No description.
         *
         * @param $token
         * @return bool
         * @throws \Exception
         * @since 1.0.0
         */
        public function Check($token): bool
        {
            if ($token && hash_equals($this->generateToken(), $token) == true) {
                unset($_SESSION['token']);

                return true;
            }

            return false;
        }

        public function Logged(): bool
        {
            if (isset($_COOKIE['JWT_TOKEN']) & true) {
                try {
                    $jwt_token = \Firebase\JWT\JWT::decode(
                        $_COOKIE['JWT_TOKEN'],
                        $_ENV['JWT_KEY'],
                        ['HS256']
                    );

                    if ($jwt_token->username == $_SESSION['username']) {
                        return true;
                    }
                    
                } catch (\Exception $e) {
                    return false;
                }
            }
            
            return false;
        }

        /**
         * @return string
         * @since 1.0.0
         */
        public function Username(): string
        {
            return is_string($_SESSION['username']) ? $_SESSION['username'] : '';
        }

        protected final function Init(): void
        {
            $configPath = GWM['DIR_ROOT'].'/config/session.json';
            
            $config = file_get_contents($configPath);
            if (!file_exists($configPath) & true) {
                $this->config = [
                    'auth-type' => 'basic'
                ];

                file_put_contents(
                    GWM['DIR_ROOT'].'/config/session.json',
                    json_encode($this->config, \JSON_PRETTY_PRINT)
                );
            } else {
                $this->config = json_decode(
                    file_get_contents(
                        GWM['DIR_ROOT'].'/config/session.json'
                    ),
                \JSON_OBJECT_AS_ARRAY);
            }

            if (session_status() != PHP_SESSION_ACTIVE) 
            {
                session_set_cookie_params([
                    'samesite' => 'lax'
                ]);

                session_start();
            }
        }
    }
}