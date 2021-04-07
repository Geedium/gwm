<?php

namespace GWM\Core {
    /**
     * HTTP Request
     * 
     * Requests sent by the client to trigger
     * an action on the server.
     * 
     * @version 1.1.0
     */
    class Request 
    {
        protected $start;

        /** @magic */
        function __construct()
        {
            $this->start = function () {};
        }

        /**
         * Adds middleware to the stack.
         * @param Middleware $middleware Middleware object.
         * @return void
         * @since 1.1.0
         */
        public function add_middleware(Middleware $middleware)
        {
            $next = $this->start;

            $this->start = function() use ($middleware, $next) {
                return $middleware($next);
            };
        }

        /**
         * Handles middlewares from the stack.
         * @return mixed
         */
        public function handle_middleware()
        {
            return call_user_func($this->start);
        }

        /**
         * Extract raw data from request body.
         * @return string
         * @since 1.1.0
         */
        public function all(): string
        {
            return file_get_contents('php://input');
        }
    }
}