<?php

namespace GWM\Core\Services {

    use GWM\Core\Session;

    use GWM\Core\Models\User;

    /**
     * Auth
     * 
     * No description.
     * 
     * @version 1.1.0
     */
    class Auth
    {
        private User $user;

        /** @magic */
        public function __construct(User $user)
        {
            $this->user = $user;
        }

        /**
         * Whether a user is logged in.
         * @return boolean
         */
        public function is_logged(): bool
        {
            return Session::Get()->Logged();
        }

        /**
         * Whether a user has a certain role.
         * @param string $name Role to find.
         * @return boolean
         * @since 1.1.0
         */
        public function has_role(string $name): bool
        {
            return in_array($name, $this->user->getRoles(), false);
        }
    }
}