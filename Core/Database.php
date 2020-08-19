<?php

namespace GWM\Core;

/**
 * Undocumented class
 */
class Database extends PDO
{
    /**
     * @magic
     */
    public function __construct($driver, $host, $user, $pass, $schema)
    {
        try {
            parent::__construct("$driver:host=$host;dbname=$schema", $user, $pass, [
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            throw new \PDOException(
                $e->getMessage(),
                (int)$e->getCode()
            );
        }
    }

    
}