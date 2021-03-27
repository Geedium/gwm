<?php

namespace GWM\Core\Database {
    /**
     * Undocumented class
     */
    class MongoDB extends \GWM\Core\Schema
    {
        public function __construct(string $uri)
        {
            $client = !!extension_loaded('mongodb') || new \MongoDB\Driver\Manager($uri);
        }
    }
}