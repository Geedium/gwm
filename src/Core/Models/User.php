<?php

namespace GWM\Core\Models;

use GWM\Core\Model;
use GWM\Core\Schema;
use GWM\Core\Session;

/**
 * Class User
 *
 * No description.
 *
 * @package GWM\Core\Models
 * @version 1.0.0
 */
class User extends Model
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
    public string $email;

    /**
     * Undocumented variable
     *
     * @var string (255)
     */
    public string $firstname;

    /**
     * Undocumented variable
     *
     * @var string (255)
     */
    public string $lastname;

    /**
     * Undocumented variable
     *
     * @var string (255)
     */
    public string $password;
    
    /**
     * Undocumented variable
     *
     * @var string (255)
     */
    public string $token;

    /**
     * @var string (255)
     */
    public ?string $role = '';

    /**
     * Undocumented variable
     *
     * @var string (20)
     */
    public string $algorithm;

    /**
     * Undocumented variable
     *
     * @var string (255)
     */
    public string $avatar;

    /**
     * Undocumented variable
     *
     * @var \DateTime
     */
    public $created_at;

    /**
     * PHPSESSID
     *
     * @var string (256)
     */
    public $session_id;

    /**
     * @magic
     * @throws \Exception
     */
    function __construct()
    {
        if (!$this->created_at) {
            $timezone = new \DateTimeZone('Europe/Vilnius');
            $datetime = new \DateTime('now', $timezone);
            $date = $datetime->format("Y-m-d");
            $this->created_at = $date;
        }
    }

    function __get($value) 
    {
        if($value == 'roles') {
            return $this->getRoles();
        }
        return $value;
    }

    function _INIT($schema)
    {
        $this->schema = $schema;
        $this->table = 'users';

        $schema->Create(User::class, $this->table);
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

    public function setSessionID(string $session_id) : self
    {
        $this->session_id = $session_id;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $firstname
     * @return self
     * @since 1.0.0
     */
    public function setFirstname(string $firstname) : self
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $avatar
     * @return self
     */
    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $lastname
     * @return self
     * @since 1.0.0
     */
    public function setLastname(string $lastname) : self
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $email
     * @return self
     * @since 1.0.0
     */
    public function setEmail(string $email) : self
    {
        $this->email = $email;
        return $this;
    }

    function All()
    {
        return $this->schema->Select($this->table, 'id', 'username', 'created_at') ?? [];
    }

    /**
     * Undocumented function
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getUserName() :? string
    {
        return $this->username ?? '';
    }

    public function getRoles(): array
    {
        return json_decode($this->role, true) ?? [];
    }

    /**
     * Undocumented function
     *
     * @param string $password
     * @return self
     */
    public function setPassword(string $password) : self
    {
        $password = password_hash($password, PASSWORD_BCRYPT);
        $this->password = is_string($password) ? $password : '';
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
        return $schema->InsertUser($this->username, $this->password, $this->email, $this->firstname, $this->lastname, $this->token, $this->session_id);
    }
}