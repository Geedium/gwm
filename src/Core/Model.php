<?php

namespace GWM\Core {
    /**
     * Class Model
     * @package GWM\Core
     */
    class Model
    {
        protected Schema $schema;
        protected string $table;

        /**
         * All
         *
         * @return array
         */
        function All()
        {
            return $this->schema->Select($this->table) ?? [];
        }
    }
}