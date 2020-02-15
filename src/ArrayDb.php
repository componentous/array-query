<?php


namespace Componentous\ArrayQuery;


class ArrayDb implements ArrayDbInterface
{
    protected array $arrays = [];

    public function addTable(string $name, array $array): bool
    {
        if (isset($this->arrays[$name])) {
            return false;
        } else {
            $this->arrays[$name] = $array;
            return true;
        }
    }

    public function hasTable(string $name): bool
    {
        return isset($this->arrays[$name]);
    }

    public function hasColumn(string $column): array
    {
        $tables = [];
        foreach ($this->arrays as $tableName => $table) {
            if (array_column($table, $column)) {
                $tables[] = $tableName;
            }
        }
        return $tables;
    }

    public function tableHasColumn(string $table, string $column): bool
    {
        return isset($this->arrays[$table][0][$column]);
    }

    public function getTable(string $name): ?array
    {
        return $this->arrays[$name] ?? null;
    }

    public function dropTable(string $name): bool
    {
        if (isset($this->arrays[$name])) {
            unset($this->arrays[$name]);
            return true;
        } else {
            return false;
        }
    }
}