<?php

namespace GWM\Core;

use GWM\Core\Errors\Basic;
use GWM\Core\Utils\Debug;
use function foo\func;

/**
 * Undocumented class
 * 
 * @version 1.0.0
 */
class Schema extends \PDO
{
    public $update;
    private $sid;

    public static self $PRIMARY_SCHEMA;

    //const USE_MONGO = false;

    /**
     * Undocumented function
     *
     * @param string $name
     * @param array $options
     * @throws Basic
     */
    public function __construct(string $name = null, array $options = null)
    {
        $name == null && $name = $_ENV['DB_NAME'];

        /*
        if (!extension_loaded('mongodb')) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                dl('php_mongodb.dll');
            } else {
                dl('mongodb.so');
            }
        }
        */

        if (!$options) {
            try {
                parent::__construct(
                    "{$_ENV['DB_DRIVER']}:host={$_ENV['DB_HOST']};dbname=$name",
                    $_ENV['DB_USERNAME'],
                    $_ENV['DB_PASSWORD']
                );
            } catch (\PDOException $e) {
                throw new Basic($e->getMessage(), false);
            }
        } else {
            if (count($options) < 4) {
                die("Not enough parameters to bind schema.");
            }

            try {
                parent::__construct(
                    "{$options['driver']}:host={$options['host']}$name",
                    $options['username'],
                    $options['password']
                );
            } catch (\PDOException $e) {
                throw new \PDOException(
                    $e->getMessage(),
                    (int)$e->getCode()
                );
            }
        }
    }

    /**
     * @param string $model_name
     * @param object $model
     * @param array $where
     * @param callable|null $callback
     * @return mixed
     * @version 1.0.0
     */
    public function Get(string $model_name, object $model, array $where, callable $callback = null)
    {
        array_walk($where, function (&$value, $key) {
            $value = "$key = $value";
        });

        $where = implode('AND', $where);

        try {
            $model_name_st = strtolower((new \ReflectionClass($model_name))->getShortName()) . 's';
            $st = parent::query("SELECT * FROM `{$_ENV['DB_PREFIX']}_$model_name_st` WHERE $where LIMIT 1");
            $object = $st->fetchObject($model_name);
            if($callback != null) {
                $callback($object);
            }
            return $object;
        } catch (\ReflectionException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    function Count(string $model_name, array $where = null)
    {
        if($where != null) {
            array_walk($where, function (&$value, $key) {
                $value = "$key = $value";
            });

            $where = 'WHERE ' . implode('AND', $where);
        } else {
            $where = '';
        }

        try {
            $model_name_st = strtolower((new \ReflectionClass($model_name))->getShortName()) . 's';

            $st = parent::prepare("SELECT COUNT(*) FROM `{$_ENV['DB_PREFIX']}_$model_name_st` $where");
            $st->execute();
            return  $st->fetchColumn();

        } catch (\ReflectionException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    function Date(string $model_name, array $where)
    {
        array_walk($where, function (&$value, $key) {
            $value = "$key = $value";
        });

        $where = implode('AND', $where);

        try {
            $model_name_st = strtolower((new \ReflectionClass($model_name))->getShortName()) . 's';

            $st = parent::prepare("SELECT MONTH(`created_at`) MONTH, COUNT(*) COUNT
                FROM `{$_ENV['DB_PREFIX']}_$model_name_st` 
                WHERE $where
                GROUP BY MONTH(`created_at`)
            ");
            $st->execute();

            return $st->fetchAll(self::FETCH_KEY_PAIR);

        } catch (\ReflectionException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * Get all objects from schema.
     * @param string|array $model_name Model Name.
     * @param string ...$criteria Search Criteria.
     * @return array Gets model class.
     * @throws \ReflectionException
     * @since 1.0.0
     */
    function All($model_name, string ...$criteria): array
    {
        if(is_array($model_name))
        {
            $model_name_st = $model_name[0];
            $model_name = $model_name[1];
        } else {
            $model_name_st = strtolower((new \ReflectionClass($model_name))->getShortName()) . 's';
        }

        return parent::query("SELECT * FROM `{$_ENV['DB_PREFIX']}_$model_name_st`")
            ->fetchAll(\PDO::FETCH_CLASS, $model_name, $criteria);
    }

    function addColumn($column, $type, $table)
    {
        parent::query("ALTER TABLE $table ADD $column $type");
    }

    public function Create($model, $table)
    {
        try {
            new Annotations($model, $data);
        } catch (\ReflectionException $e) {
            die($e->getMessage());
        }

        try {
            if (!$this->Exists('TABLES', "{$_ENV['DB_PREFIX']}_$table")) {
                try {
                    $sql = "CREATE TABLE `{$_ENV['DB_PREFIX']}_$table` (";
                    $sql .= "`id` INT PRIMARY KEY AUTO_INCREMENT";

                    /* @TODO: Insert fields in dB
                    foreach ($data as $property) {
                        foreach ($property as $key => $value) {

                        }
                    }
                    */

                    $sql .= ") ENGINE = InnoDB";

                    $results = parent::query($sql);
                    return true;
                } catch (\Exception $e) {
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
                        }

                        $id = $this->sid;

                        if ($id == '') {
                            die('Primary key can not be NULL!');
                        }

                        if (!$this->Exists($tablename, $key, 'COLUMNS FROM')) {
                            $value[0] = ltrim($value[0]);
                            $value[0] = rtrim($value[0]);

                            switch ($value[0]) {
                                case 'string|null':
                                    $value[1] = preg_replace('/\s+/', '', $value[1]);

                                    if ($value[1] == 'text') {
                                        $this->addColumn($key, "TEXT NULL DEFAULT NULL", $tablename);
                                    } else {
                                        $this->addColumn($key, "VARCHAR($value[1]) NULL DEFAULT NULL", $tablename);
                                    }
                                    break;
                                case 'string':
                                {
                                    $value[1] = preg_replace('/\s+/', '', $value[1]);

                                    if ($value[1] == 'text') {
                                        $this->addColumn($key, "TEXT NOT NULL", $tablename);
                                    } else if($value[1] == 'decimal="10.2"') {
                                        $this->addColumn($key, "DECIMAL(10,2) NOT NULL DEFAULT 0", $tablename);
                                    } else {
                                        $this->addColumn($key, "VARCHAR($value[1]) NOT NULL", $tablename);
                                    }
                                    break;
                                }
                                case 'bool':
                                {
                                    $this->addColumn($key, "TINYINT(3) NOT NULL DEFAULT 0", $tablename);
                                    break;
                                }
                                case 'int':
                                {
                                    $value[1] = preg_replace('/\s+/', '', $value[1]);

                                    if ($value[1] == 'primary') {
                                        $this->addColumn($key, "INT PRIMARY KEY AUTO_INCREMENT", $tablename);
                                    } else {
                                        $this->addColumn($key, "INT(11) NOT NULL DEFAULT 0", $tablename);
                                    }

                                    break;
                                }
                                case '\DateTime':
                                {
                                    $this->addColumn($key, "DATE NULL DEFAULT NULL", $tablename);
                                    break;
                                }
                            }
                        }
                    }

                    $this->update = parent::prepare("UPDATE $tablename SET title = ?, content = ? WHERE $id = ?");
                } catch (\Exception $e) {
                    die($e->getMessage());
                }
            }
        } catch (Basic $e) {
            die($e->getMessage());
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Compare
     *
     * @param $table
     * @param array $columns
     * @param mixed ...$rows
     */
    public function Compare($table, array $columns, ...$rows)
    {
        array_walk($rows, function (&$value, $key) use ($columns) {
            $value = "$columns[$key] = '$value' ";
        });

        $columns = implode(',', $columns);
        $rows = implode(' AND ', $rows);

        $pdo = parent::prepare("SELECT $columns FROM {$_ENV['DB_PREFIX']}_$table WHERE $rows LIMIT 1");
        $pdo->execute();

        return $pdo->fetchAll(self::FETCH_ASSOC);
    }

    public function InsertUser(string $username, string $password, string $email, string $firstname, string $lastname, string $token)
    {
        $table = "{$_ENV['DB_PREFIX']}_users";
        $pdo = parent::prepare("INSERT INTO $table (username, password, algorithm, created_at, avatar, role, firstname, lastname, email, token) 
        VALUES(:user, :pass, :algo, :date, :avatar, :role, :firstname, :lastname, :email, :token)");

        $timezone = new \DateTimeZone('Europe/Vilnius');
        $datetime = new \DateTime('now', $timezone);
        $date = $datetime->format("Y-m-d");

        $pdo->execute([
            'user' => $username, 
            'pass' => $password, 
            'algo' => 'google',
            'date' => $date,
            'avatar' => '', 
            'role' => 'user',
            'firstname' => $firstname, 
            'lastname' => $lastname, 
            'email' => $email,
            'token' => $token
        ]);

        return parent::lastInsertId();
    }

    public function Insert(string $table, string $title, $date, string $content)
    {
        $tablename = "{$_ENV['DB_PREFIX']}_$table";
        $pdo = parent::prepare("INSERT INTO $tablename (title, created_at, content) VALUES (?, ?, ?)");
        $pdo->execute([$title, $date, $content]);
        return parent::lastInsertId();
    }

    public function Select(string $table, string $orderBy = null, string ...$params)
    {
        $tablename = "{$_ENV['DB_PREFIX']}_$table";

        if($orderBy != null) {
            $orderBy = 'ORDER BY '.$orderBy;
        }

        $statement = "SELECT * FROM $tablename $orderBy";

        if($params ?? false) {
            $params = implode(',', $params);
            $statement = "SELECT {$params} FROM $tablename $orderBy";
        }

        $pdo = parent::prepare($statement);
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
     * @throws \Exception
     * @since 1.0.0
     */
    public function Save(string ...$params)
    {
        try {
            $this->update->execute($params);
            return true;
        } catch (\Exception $e) {
            throw new Basic($e->getMessage(), true);
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
     * @return boolean
     * @throws Basic
     * @throws \Exception
     */
    public function Exists(string $access, string $name, string $alt = '') : bool
    {
        try {
            $query = "SHOW $alt $access LIKE '%$name%' ";
            $results = parent::query($query);
            return $results->rowCount() > 0;
        }
        catch(\Exception $e) {
            throw new Basic($e->getMessage(), true);
        }
    }

}