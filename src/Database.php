<?php


namespace Componentous\ArrayQuery;


class Database implements DatabaseInterface
{
    /** @var Table[] */
    protected array $tables = [];

    public function addTable(string $name, array $data): bool
    {
        if (isset($this->tables[$name])) {
            return false;
        } else {
            $this->tables[$name] = new Table($name, $data);
            return true;
        }
    }

    public function hasTable(string $name): bool
    {
        return isset($this->tables[$name]);
    }

    public function hasColumn(string $column): array
    {
        $tables = [];
        foreach ($this->tables as $tableName => $table) {
            if ($table->hasColumn($column)) {
                $tables[] = $tableName;
            }
        }
        return $tables;
    }

    public function tableHasColumn(string $table, string $column): bool
    {
        return isset($this->tables[$table]) && $this->tables[$table]->hasColumn($column);
    }

    public function getTable(string $name): ?TableInterface
    {
        return $this->tables[$name] ?? null;
    }

    public function dropTable(string $name): bool
    {
        if (isset($this->tables[$name])) {
            unset($this->tables[$name]);
            return true;
        } else {
            return false;
        }
    }
}