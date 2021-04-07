<?php

namespace GWM\Core {
    /**
     * Middleware
     * 
     * @version 1.1.0
     */
    interface Middleware
    {
        /** @magic */
        public function __invoke(callable $next);
    }
}