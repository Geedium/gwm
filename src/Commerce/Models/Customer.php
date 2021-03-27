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
        public ?User $user = null;

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
    }
}