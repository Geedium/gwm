<?php

/**
 * Structured Query Language
 * 
 * SQL is a domain-specific language used
 * in programming and designed for
 * managing data held in a relational
 * database management system, or for
 * stream processing in a relational data
 * stream management system.
 * 
 * @version 1.1.0
 */
class SQL
{
    /** @var array Used to specify which columns needs to be extracted. */
    private array $fields = [];

    /** @var array Used to specify which table to select or delete data from. */
    private array $from = [];

    /** @var array Used to filter records. */
    private array $where = [];

    /**
     * Assigns fields to SELECT command.
     * @param array $fields Fields.
     * @return SQL
     * @since 1.1.0
     */
    public function select(array $fields): SQL
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * Adds table with alias to FROM command.
     * @param string $table Table name.
     * @param string $alias Table alias.
     * @return SQL
     * @since 1.1.0
     */
    public function from(string $table, string $alias): SQL
    {
        $this->from[] = "$table AS $alias";
        return $this;
    }

    /**
     * Assigns condition to WHERE command.
     * @param string $condition Condition.
     * @return Sql
     * @since 1.1.0
     */
    public function where(string $condition): Sql
    {
        $this->where[] = $condition;
        return $this;
    }

    /** @magic */
    public function __toString(): string
    {
        return sprintf(
            'SELECT %s FROM %s WHERE %s',
            join(', ', $this->fields),
            join(', ', $this->from),
            join(' AND ', $this->where)
        );
    }
}