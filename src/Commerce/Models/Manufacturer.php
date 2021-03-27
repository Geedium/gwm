<?php

namespace GWM\Commerce\Models {
    use GWM\Core\ { Model, Schema };

    /**
     * Class Manufacturer
     * @package GWM\Commerce\Models
     * @version 1.0.0
     */
    class Manufacturer extends Model
    {
        /**
         * Undocumented variable
         *
         * @var int (primary)
         */
        public int $id = 0;

        /**
         * Undocumented variable
         *
         * @var string|null(255)
         */
        public ?string $title = null;



        function __construct()
        {
            Schema::$PRIMARY_SCHEMA->Create(Manufacturer::class, 'manufacturers');
        }

        function __toString()
        {
            return $this->title ?? 'No Manufacturer';
        }
    }
}