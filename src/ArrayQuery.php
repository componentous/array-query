<?php


namespace Componentous\ArrayQuery;


use InvalidArgumentException;
use RuntimeException;

class ArrayQuery
{
    protected ArrayDb $db;

    protected string $table;
    protected array $tables = [];
    protected array $columns = [];

    public function getTable(): string
    {
        return $this->table;
    }

    public function getTables(): array
    {
        return $this->tables;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }


    public function __construct(ArrayDb $db)
    {
        $this->db = $db;
    }

    public function getDb(): ArrayDb
    {
        return $this->db;
    }

    public function select(string ...$columns): self
    {
        foreach ($columns as $column) {
            $tablesWithColumn = $this->db->hasColumn($column);
            if (!$tablesWithColumn) {
                throw new InvalidArgumentException("Column '$column' does not exist.");
            } elseif (count($tablesWithColumn) > 1) {
                throw new InvalidArgumentException("Ambiguous column '$column'");
            }
            $table = reset($tablesWithColumn);
            $this->columns[$table][] = $column;
        }
        return $this;
    }

    public function from(string $table): self
    {
        if (!$this->db->hasTable($table)) {
            throw new InvalidArgumentException("Table '$table' does not exist");
        }
        $this->table = $table;
        $this->tables[$table] = true;
        return $this;
    }

    public function validateColumns(): void
    {
        foreach ($this->columns as $table => $columns) {
            if (!isset($this->tables[$table])) {
                $columnNames = implode(', ', $columns);
                $errors[] = "Column(s) $columnNames are in table $table that was not included in the from clause";
            }
        }
        if (isset($errors)) {
            throw new RuntimeException('Invalid columns: ' . implode('; ', $errors));
        }
    }

    public function getResult(): array
    {
        $this->validateColumns();
        $result = [];
        foreach ($this->columns as $table => $columns) {
            $table = $this->db->get($table);
            foreach ($table as $i => $row) {
                foreach ($columns as $column) {
                    $result[$i][$column] = $row[$column] ?? null;
                }
            }

        }
        return $result;
    }

}