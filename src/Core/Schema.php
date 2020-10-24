<?php

namespace GWM\Core;

use GWM\Core\Exceptions\Basic;
use GWM\Core\Utils\Debug;

/**
 * Undocumented class
 * 
 * @version 1.0.0
 */
class Schema extends \PDO
{
    public $update;
    private $sid;

    /**
     * Undocumented function
     *
     * @param string $name
     * @param array $options
     * @throws Basic
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
            } catch (\PDOException $e) {
                throw new Basic($e->getMessage(), true);
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

        if (!$this->Exists('TABLES', "{$_ENV['DB_PREFIX']}_$table")) {
            try {
                $sql = "CREATE TABLE `{$_ENV['DB_PREFIX']}_$table` (";

                foreach ($data as $property) {
                    foreach ($property as $key => $value) {
                        echo $value;
                        if (strcasecmp($value, '@string') == 3) {
                            //  if (!$this->Exists("`{$_ENV['DB_PREFIX']}_$table`", $name, 'COLUMNS FROM')) {
                            $sql .= "`$value` VARCHAR(255) NULL DEFAULT NULL";
                            //  }
                        }
                    }
                }

                $sql .= ")";

                $results = parent::query($sql);
                return true;
            } catch (Exception $e) {
                die($e->getMessage());
            }
        } else {
            try {
                $tablename = "{$_ENV['DB_PREFIX']}_$table";

                foreach ($data as $key => $value) {
                    $value[0] = preg_replace('/\s+/', '', $value[0]);

                    switch ($value[0]) {
                        case 'string':
                        {
                            break;
                        }
                        case 'int':
                        {
                            $value[1] = preg_replace('/\s+/', '', $value[1]);

                            if ($value[1] == 'primary') {
                                $this->sid = $key;
                            }
                            break;
                        }
                        case 'DateTime':
                        {
                            break;
                        }
                    }

                    $id = $this->sid;

                    if ($id == '') {
                        die('Primary key can not be NULL!');
                    }

                    if (!$this->Exists($tablename, $key, 'COLUMNS FROM')) {
                        // Remove whitespaces from a string.

                        switch ($value[0]) {
                            case 'string':
                            {
                                $value[1] = preg_replace('/\s+/', '', $value[1]);

                                if ($value[1] == 'text') {
                                    $this->addColumn($key, "TEXT NULL DEFAULT NULL", $tablename);
                                } else {
                                    $this->addColumn($key, "VARCHAR($value[1]) NULL DEFAULT NULL", $tablename);
                                }
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

                $this->update = parent::prepare("UPDATE $tablename SET title = ?, content = ? WHERE $id = ?");
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }
    }

    public function InsertUser(string $username, string $password)
    {
        $table = "{$_ENV['DB_PREFIX']}_users";
        $pdo = parent::prepare("INSERT INTO $table (username, password) VALUES(?, ?)");
        $pdo->execute([$username, $password]);
        return parent::lastInsertId();
    }

    public function Insert(string $table, string $title, $date, string $content)
    {
        $tablename = "{$_ENV['DB_PREFIX']}_$table";
        $pdo = parent::prepare("INSERT INTO $tablename (title, created_at, content) VALUES (?, ?, ?)");
        $pdo->execute([$title, $date, $content]);
        return parent::lastInsertId();
    }

    public function Select(string $table)
    {
        $tablename = "{$_ENV['DB_PREFIX']}_$table";
        $pdo = parent::prepare("SELECT * FROM $tablename");
        $pdo->execute();

        $result = $pdo->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Save or update changes to the database.
     *
     * @param string ...$params
     * @return bool
     * @throws Basic
     * @throws Exception*@since 1.0.0
     */
    public function Save(string ...$params)
    {
        try {
            $this->update->execute($params);
            return true;
        } catch (\Exception $e) {
            throw new Basic($e->getMessage(), true);
        }
        return false;
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
     * @return boolean
     * @throws Basic
     * @throws \Exception
     */
    public function Exists(string $access, string $name, string $alt = '') : bool
    {
        try {
            $query = "SHOW $alt $access LIKE '$name'";
            $results = parent::query($query);
            return $results->rowCount() > 0;
        }
        catch(\Exception $e) {
            throw new Basic($e->getMessage(), true);
        }
    }

}