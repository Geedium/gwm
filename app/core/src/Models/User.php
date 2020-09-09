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
     * @var DateTime
     */
    public $created_at;

    /**
     * @magic
     */
    function __construct(&$schema)
    {
        $this->created_at = (new \DateTime())->format("Y-m-d");

        $schema->Create(User::class, 'users');

        return function () use ($username, $password) {
            $username = \filter_var($username, FILTER_SANITIZE_STRING);
            $password = \filter_var($password, PASSWORD_DEFAULT);
        };
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    function isLoggedIn()
    {

    }
}