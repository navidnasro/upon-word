<?php

namespace engine\database;

use engine\database\enums\OutputType;
use engine\database\enums\Table;

defined('ABSPATH') || exit;

class QueryBuilder
{
    private string $query;

    public function __construct()
    {
        $this->query = '';
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

    public function from(Table $table): QueryBuilder
    {
        global $wpdb;

        $this->query .= ' FROM '.$wpdb->base_prefix.$table->value;

        return $this;
    }

    public function where(string $column,string $operator,string $value2): QueryBuilder
    {
        $this->query .= ' WHERE '.$column.' '.$operator.' "'.$value2.'"';

        return $this;
    }

    public function andWhere(string $value1,string $operator,string $value2): QueryBuilder
    {
        $this->query .= ' AND '.$value1.' '.$operator.' '.$value2;

        return $this;
    }

    public function orWhere(string $value1,string $operator,string $value2): QueryBuilder
    {
        $this->query .= ' OR '.$value1.' '.$operator.' '.$value2;

        return $this;
    }

    public function notWhere(string $value1,string $operator,string $value2): QueryBuilder
    {
        $this->query .= ' NOT '.$value1.' '.$operator.' '.$value2;

        return $this;
    }

    public function getVar(int $column = 0,int $row = 0): ?string
    {
        global $wpdb;

        return $wpdb->get_var($wpdb->prepare($this->query),$column,$row);
    }

    public function getRow(OutputType $outputType = OutputType::OBJECT,int $row = 0): object|array|null
    {
        global $wpdb;

        return $wpdb->get_row($wpdb->prepare($this->query),$outputType->value,$row);
    }

    public function getColumn(int $col = 0): array
    {
        global $wpdb;

        return $wpdb->get_col($wpdb->prepare($this->query),$col);
    }

    public function getResults(OutputType $outputType = OutputType::OBJECT): array|object|null
    {
        global $wpdb;

        return $wpdb->get_results($wpdb->prepare($this->query),$outputType->value);
    }

    public function resetQuery(): void
    {
        $this->query = '';
    }
}