<?php

namespace GWM\Commerce\Models
{
    /**
     * Class Category
     * @package GWM\Commerce\Models
     * @version 1.0.0
     */
    class Category
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
         * @var string(255)
         */
        public string $name;

        /**
         * Undocumented variable
         *
         * @var int
         */
        public ?int $ref;

        function _INIT($schema)
        {
            $this->schema = $schema;
            $this->table = 'categories';

            $schema->Create(Category::class, $this->table);
        }
    }
}