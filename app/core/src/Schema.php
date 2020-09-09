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

    function addColumn($column, $type, $table)
    {
        parent::query("ALTER TABLE $table ADD $column $type");
    }

    public function Create($model, $table)
    {
        new Annotations($model, $data);

        if(!$this->Exists('TABLES', "{$_ENV['DB_PREFIX']}_$table"))
        {
            try {
                $sql = "CREATE TABLE `{$_ENV['DB_PREFIX']}_$table` (";

                foreach($data as $property)
                {
                    foreach($property as $key => $value)
                    {
                        echo $value;
                        if (strcasecmp($value, '@string') == 3) {
                            //  if (!$this->Exists("`{$_ENV['DB_PREFIX']}_$table`", $name, 'COLUMNS FROM')) {
                            $sql .= "`$name` VARCHAR(255) NULL DEFAULT NULL";
                            //  }
                        }   
                    }
                }

                $sql .= ")";

                $results = parent::query($sql);
                return true;
            }
            catch(Exception $e)
            {
                die($e);
            }
        } else {
            try {
                $tablename = "{$_ENV['DB_PREFIX']}_$table";

                foreach($data as $key => $value)
                {
                    if (!$this->Exists($tablename, $key, 'COLUMNS FROM')) {
                        // Remove whitespaces from a string.
                        $value[0] = preg_replace('/\s+/', '', $value[0]);

                        switch ($value[0]) {
                        case 'string':
                            {
                                $this->addColumn($key, "VARCHAR($value[1]) NULL DEFAULT NULL", $tablename);
                                break;
                            }
                        case 'int':
                            {
                                $value[1] = preg_replace('/\s+/', '', $value[1]);
                                
                                if ($value[1] == 'primary') {
                                    $this->addColumn($key, "INT PRIMARY KEY AUTO_INCREMENT", $tablename);
                                }
                                break;
                            }
                            case 'DateTime':
                            {
                                $this->addColumn($key, "DATE NULL DEFAULT NULL", $tablename);
                                break;
                            }
                        }
                    }
                }
            }
            catch(Exception $e)
            {
                die($e);
            }
        }
    }

    /*
            $query = parent::prepare("SELECT * FROM $table");
        $query->execute();
        $rows = $query->fetchAll(\PDO::FETCH_ASSOC);
        return $rows;
        */

    /**
     * Undocumented function
     *
     * @param string $access
     * @param string $name
     * @throws Exception
     * @return boolean
     */
    public function Exists(string $access, string $name, string $alt = '') : bool
    {
        try {
            $query = "SHOW $alt $access LIKE '$name'";
            $results = parent::query($query);
            return $results->rowCount() > 0;
        }
        catch(Exception $e)
        {
            die($e);
        }
    }

}