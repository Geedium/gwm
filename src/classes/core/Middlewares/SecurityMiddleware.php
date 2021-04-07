<?php

namespace GWM\Core\Middlewares
{
    use GWM\Core\Middleware;

    /**
     * Security Middleware
     * 
     * .
     * 
     * @version 1.1.0
     */
    class SecurityMiddleware implements Middleware
    {
        /** @magic */
        function __invoke(callable $next)
        { 
            if (PHP_OS_FAMILY == 'Linux') {
                $load = sys_getloadavg();

                if ($load[0] > 0.80) {
                    header('HTTP/1.1 503 Too busy, try again later');
                    die('Server too busy. Please try again later.');
                }
            }

            return $next();
        }
    }
}