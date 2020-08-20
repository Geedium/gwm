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

    public function Create($model, $table)
    {
        if(!$this->Exists('TABLES', "{$_ENV['DB_PREFIX']}_$table"))
        {
            $vars = get_class_vars($model);

            $data = [];

            foreach ($vars as $name => $value) {
                $comment_string = (new \ReflectionClass($model))->getProperty('username')->getDocComment();
                $pattern = "#(@[a-zA-Z]+\s*[a-zA-Z0-9, ()_].*)#";
                preg_match_all($pattern, $comment_string, $matches, PREG_PATTERN_ORDER);
              
                $data[$name] = $matches[0][0];
            }

            try {
                $sql = "CREATE TABLE `{$_ENV['DB_PREFIX']}_$table` (";

                foreach($data as $name => $value)
                {
                    if (strcasecmp($value, '@string') == 3) {
                        $sql .= "`$name` VARCHAR(255) NULL DEFAULT NULL";
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
        }
    }

    /**
     * Undocumented function
     *
     * @param string $access
     * @param string $name
     * @throws Exception
     * @return boolean
     */
    public function Exists(string $access, string $name) : bool
    {
        try {
            $results = parent::query("SHOW $access LIKE '$name'");
            return $results->rowCount() > 0;
        }
        catch(Exception $e)
        {
            die($e);
        }
    }

}