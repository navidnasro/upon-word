<?php

namespace engine\database;

use engine\database\enums\OutputType;
use engine\database\enums\Table;

defined('ABSPATH') || exit;

class QueryBuilder
{
    private string $query;
    private string $prefix;
    private static ?QueryBuilder $instance = null;

    private function __construct()
    {
        global $wpdb;

        $this->query = '';
        $this->prefix = $wpdb->base_prefix;
    }

    public static function getInstance(): QueryBuilder
    {
        if (is_null(self::$instance))
        {
            self::$instance = new self();
        }

        self::$instance->resetQuery();
        return self::$instance;
    }

    public function select(string ...$columns): QueryBuilder
    {
        $this->query .= 'SELECT ';

        foreach ($columns as $column)
        {
            $this->query .= $column;

            if (end($columns) != $column)
                $this->query .= ',';
        }

        return $this;
    }

    public function insert(array ...$values): QueryBuilder
    {
        $this->query .= ' VALUES ';

        foreach ($values as $row)
        {
            $this->query .= '(';

            foreach ($row as $value)
            {
                $this->query .= '"'.$value.'"';

                if (end($row) != $value)
                    $this->query .= ',';

                else
                    $this->query .= ')';
            }

            if (end($values) != $row)
                $this->query .= ',';

            else
                $this->query .= ';';
        }

        return $this;
    }

    public function into(Table $table,string ...$columns): QueryBuilder
    {
        $values = $this->query;

        $this->resetQuery();

        $this->query .= 'INSERT INTO '.$this->prefix.$table->value.' (';

        foreach ($columns as $column)
        {
            $this->query .= $column;

            if (end($columns) != $column)
                $this->query .= ',';

            else
                $this->query .= ')';
        }

        $this->query .= ' '.$values;

        return $this;
    }

    public function from(Table $table,string $tableAlias = ''): QueryBuilder
    {
        $this->query .= ' FROM '.$this->prefix.$table->value;

        if (!empty($tableAlias))
            $this->query .= ' AS '.$tableAlias;

        return $this;
    }

    public function innerJoin(Table $table,string $tableAlias = ''): QueryBuilder
    {
        $this->query .= ' INNER JOIN '.$this->prefix.$table->value;

        if (!empty($tableAlias))
            $this->query .= ' AS '.$tableAlias;

        return $this;
    }

    public function leftJoin(Table $table,string $tableAlias = ''): QueryBuilder
    {
        $this->query .= ' LEFT JOIN '.$this->prefix.$table->value;

        if (!empty($tableAlias))
            $this->query .= ' AS '.$tableAlias;

        return $this;
    }

    public function rightJoin(Table $table,string $tableAlias = ''): QueryBuilder
    {
        $this->query .= ' RIGHT JOIN '.$this->prefix.$table->value;

        if (!empty($tableAlias))
            $this->query .= ' AS '.$tableAlias;

        return $this;
    }

    public function fullJoin(Table $table,string $tableAlias = ''): QueryBuilder
    {
        $this->query .= ' FULL OUTER JOIN '.$this->prefix.$table->value;

        if (!empty($tableAlias))
            $this->query .= ' AS '.$tableAlias;

        return $this;
    }

    public function on(Table|string $table1,string $column1,string $operator,Table|string $table2,string $column2): QueryBuilder
    {
        if ($table1 instanceof Table && $table2 instanceof Table)
            $this->query .= ' ON '.$this->prefix.$table1->value.'.'.$column1.' '.$operator.' '.$this->prefix.$table2->value.'.'.$column2;

        elseif (is_string($table1) && is_string($table2))
            $this->query .= ' ON '.$table1.'.'.$column1.' '.$operator.' '.$table2.'.'.$column2;

        return $this;
    }

    public function where(string $column,string $operator,string $value): QueryBuilder
    {
        $this->query .= ' WHERE '.$column.' '.$operator.' "'.$value.'"';

        return $this;
    }

    public function andWhere(string $value1,string $operator,string $value2): QueryBuilder
    {
        $this->query .= ' AND '.$value1.' '.$operator.' "'.$value2.'"';

        return $this;
    }

    public function orWhere(string $value1,string $operator,string $value2): QueryBuilder
    {
        $this->query .= ' OR '.$value1.' '.$operator.' "'.$value2.'"';

        return $this;
    }

    public function notWhere(string $value1,string $operator,string $value2): QueryBuilder
    {
        $this->query .= ' NOT '.$value1.' '.$operator.' "'.$value2.'"';

        return $this;
    }

    public function getVar(int $column = 0,int $row = 0): ?string
    {
        global $wpdb;

        return $wpdb->get_var($this->query,$column,$row);
    }

    public function getRow(OutputType $outputType = OutputType::OBJECT,int $row = 0): object|array|null
    {
        global $wpdb;

        return $wpdb->get_row($this->query,$outputType->value,$row);
    }

    public function getColumn(int $col = 0): array
    {
        global $wpdb;

        return $wpdb->get_col($this->query,$col);
    }

    public function getResults(OutputType $outputType = OutputType::OBJECT): array|object|null
    {
        global $wpdb;

        return $wpdb->get_results($this->query,$outputType->value);
    }

    public function doQuery(): void
    {
        global $wpdb;

        $wpdb->query($this->query);
    }

    public function resetQuery(): void
    {
        $this->query = '';
    }

    public function setQuery(string $query): QueryBuilder
    {
        $this->query = $query;

        return $this;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getQuery(): string
    {
        return $this->query;
    }
}