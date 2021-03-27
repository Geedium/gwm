<?php

namespace GWM\Core\Models {

    use GWM\Core\Model;

    /**
     * Class Role
     * @package GWM\Core\Models
     * @version 1.0.0
     */
    class Config extends Model
    {
        /**
         * Undocumented variable
         *
         * @var string (255)
         */
        public string $key;

        /**
         * Undocumented variable
         *
         * @var string (255)
         */
        public string $value;
    }
}