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
        /**
         * Session destructor.
         * @magic
         */
        function __destruct()
        {

        }

        function Logout()
        {
            if (session_status() != PHP_SESSION_ACTIVE)
                session_start();

            session_unset();
            session_destroy();
        }

        /**
         * Undocumented function
         *
         * @return string
         * @throws \Exception
         */
        public function generateToken(): string
        {
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
            if (hash_equals($this->generateToken(), $token) == true) {
                unset($_SESSION['token']);

                return true;
            }

            return false;
        }

        public function Logged(): bool
        {
            return !empty($_SESSION['username'] ?? '');
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
            if (session_status() == PHP_SESSION_ACTIVE) {
                session_regenerate_id();
            } else {
                session_set_cookie_params([
                    'samesite' => 'lax'
                ]);

                session_start();
            }
        }
    }
}