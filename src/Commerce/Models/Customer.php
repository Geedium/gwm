<?php

namespace GWM\Commerce\Models {

    use GWM\Core\Models\User;

    /**
     * Class Customer
     * @package GWM\Commerce\Models
     * @version 1.0.0
     */
    class Customer
    {
        /**
         * Undocumented variable
         *
         * @var int (primary)
         */
        public int $id;

        /**
         * Undocumented variable
         *
         * @var int|null
         */
        protected ?int $user = null;

        /**
         * Undocumented variable
         *
         * @var int
         */
        public string $firstname;

        /**
         * Undocumented variable
         *
         * @var int
         */
        public string $lastname;

        /**
         * Undocumented variable
         *
         * @var string|null(255)
         */
        public ?string $phone_number;

        /**
         * Undocumented variable
         *
         * @var int
         */
        public int $state;

        /**
         * Undocumented variable
         *
         * @var int
         */
        public int $city;


        public function __get($propertyName): ?Customer
        {
            if ($propertyName == 'user') {
                $user = new User();
    
                if ($this->user ?? false) {
                    $user = Schema::$PRIMARY_SCHEMA->Get(User::class, $user, [
                        'id' => $this->user
                    ]);
                }
    
                return $user;
            }
    
            return null;
        }
    }
}