<?php

namespace GWM\Core\Models;

use GWM\Core\Schema;

/**
 * Class User
 *
 * No description.
 *
 * @package GWM\Core\Models
 * @version 1.0.0
 */
class User
{
    /**
     * Undocumented variable
     *
     * @var int (primary)
     */
    public int $id;

    /**
     * Undocumented variable
     *
     * @var string (255)
     */
    public string $username;

    /**
     * Undocumented variable
     *
     * @var string (255)
     */
    public string $password;

    /**
     * Undocumented variable
     *
     * @var DateTime
     */
    public $created_at;

    /**
     * @magic
     * @param $schema
     */
    function __construct($schema)
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
        $hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 15]);
        $this->password = is_string($hash) ? $hash : '';
     //   $this->password = $password;
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

    function register($schema)
    {
        return $schema->InsertUser($this->username, $this->password);
    }
}