<?php

namespace GWM\Core;

/**
 * Undocumented class
 */
class Schema
{
    private static $database;

    /**
     * Undocumented function
     *
     * @param Database $database
     * @return void
     */
    public function Bind(Database &$database) : void
    {
        self::$database = $database;
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
            $results = self::$database->query("SHOW TABLES LIKE '$table'");
            return $results->rowCount() > 0;
        }
        catch(Exception $e)
        {
            die($e);
        }
    }

}