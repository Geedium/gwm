<?php

/**
 * Request
 * 
 * [Desc].
 * 
 * @version 1.1.0
 */
class Request
{
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