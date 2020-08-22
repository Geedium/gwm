<?php

namespace GWM\Core\Models;

/**
 * Undocumented class
 */
class User
{
    /**
     * Undocumented variable
     *
     * @var int (primary)
     */
    public $id;

    /**
     * Undocumented variable
     *
     * @var string (255)
     */
    public $username;

    /**
     * Undocumented variable
     *
     * @var string (255)
     */
    public $password;

    /**
     * Undocumented variable
     *
     * @var DateTime (now)
     */
    public $created_at;

    /**
     * @magic
     */
    function __construct(&$schema)
    {
        $this->created_at = (new \DateTime())->format("Y-m-d");

        $schema->Create(User::class, 'users');
    }
}