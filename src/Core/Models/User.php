<?php

namespace GWM\Core\Models;

/**
 * Undocumented class
 * 
 * @version 1.0.0
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
    }

    /**
     * Undocumented function
     *
     * @param string $username
     * @return self
     * @since 1.0.0
     */
    public function setUserName(string $username) : self
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getUserName() :? string
    {
        return $this->username;
    }

    /**
     * Undocumented function
     *
     * @param string $password
     * @return self
     */
    public function setPassword(string $password) : self
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function getPassword() :? string
    {
        return $this->password;
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