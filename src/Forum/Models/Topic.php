<?php

namespace GWM\Forum\Models {
    /**
     * Class Topic
     * @package GWM\Forum\Models
     */
    class Topic
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
         * @var string|null (100)
         */
        public ?string $description;
    }
}