<?php

namespace GWM\Core\Models;

/**
 * Undocumented class
 */
class User
{
    /**
     * @string()
     */
    public $username;

    /**
     * @magic
     */
    function __construct(&$schema)
    {
        $schema->Create(User::class, 'users');
    }
}