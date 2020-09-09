<?php

namespace GWM\Core;

/**
 * Undocumented class
 */
class Status extends \SplEnum
{
    const __default = self::OK;

    /**
     * [description]
     */
    const OK = 200;

    /**
     * [description]
     */
    const CREATED = 201;

    /**
     * [description]
     */
    const NO_CONTENT = 204;

    /**
     * [description]
     */
    const NOT_MODIFIED = 304;

    /**
     * [description]
     */
    const BAD_REQUEST = 400;

    /**
     * [description]
     */
    const UNAUTHORIZED = 401;

    /**
     * [description]
     */
    const FORBIDDEN = 403;

    /**
     * [description]
     */
    const NOT_FOUND = 404;

    /**
     * [description]
     */
    const CONFLICT = 409;

    /**
     * [description]
     */
    const INTERNAL_SERVER_ERROR = 500;
}