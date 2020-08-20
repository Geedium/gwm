<?php

namespace GWM\Core;

/**
 * Undocumented class
 */
class Schema extends \PDO
{
    /**
     * Undocumented function
     *
     * @param string $name
     * @param array $options
     */
    public function __construct(string $name, array $options = null)
    {
        if (!$options) {
            try {
                parent::__construct(
                    "{$_ENV['DB_DRIVER']}:host={$_ENV['DB_HOST']};dbname=$name",
                    $_ENV['DB_USERNAME'],
                    $_ENV['DB_PASSWORD']
                );
            } catch (PDOException $e) {
                throw new \PDOException(
                    $e->getMessage(),
                    (int)$e->getCode()
                );
            }
        } else {
            if (count($options) < 4) {
                die("Not enough parameters to bind schema.");
            }
        
            try {
                parent::__construct(
                    "{$options['driver']}:host={$options['host']};dbname=$name",
                    $options['username'],
                    $options['password']
                );
            } catch (PDOException $e) {
                throw new \PDOException(
                    $e->getMessage(),
                    (int)$e->getCode()
                );
            }
        }
    }

    /**
     * Undocumented function
     *
     * @param string $table
     * @return boolean|Exception
     */
    public function Exists(string $table) :? bool
    {
        try {
            $results = parent::query("SHOW TABLES LIKE '$table'");
            return $results->rowCount() > 0;
        }
        catch(Exception $e)
        {
            die($e);
        }
    }

}