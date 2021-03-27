<?php

namespace GWM\Forum\Models {
    /**
     * Class Category
     * @package GWM\Forum\Models
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
         * @var string (255)
         */
        public string $title;

        /**
         * Undocumented variable
         *
         * @var Topic[] $topics
         */
        public array $topics;
    }
}