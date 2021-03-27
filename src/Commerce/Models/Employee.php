<?php

namespace GWM\Commerce\Models {

    use GWM\Core\Models\User;

    /**
     * Class Employee
     * @package GWM\Commerce\Models
     * @version 1.0.0
     */
    class Employee extends User
    {
        /**
         * Undocumented variable
         *
         * @var int (primary)
         */
        public int $id;

        /**
         *
         *
         * @var string(decimal="10.2")
         */
        public string $salary;


    }
}