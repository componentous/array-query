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
    protected array $criteria = [];
    protected string $groupBy;

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

    public function where(string $column, callable $criterion)
    {
        if (!$this->db->hasColumn($column)) {
            throw new InvalidArgumentException("Column '$column' does not exist");
        }
        $this->criteria[$column][] = $criterion;
        return $this;
    }

    public function groupBy(string $column)
    {
        if (!$this->db->hasColumn($column)) {
            throw new InvalidArgumentException("Column '$column' does not exist");
        }
        $this->groupBy = $column;
        return $this;
    }


    public function getResult(): array
    {
        $this->validateColumns();
        $result = [];
        foreach ($this->columns as $table => $columns) {
            $table = $this->db->getTable($table);
            foreach ($table as $i => $row) {
                if ($this->rowMeetsCriteria($row)) {
                    foreach ($columns as $column) {
                        $result[$i][$column] = $row[$column] ?? null;
                        if (isset($this->groupBy)) {
                            $result[$i][$this->groupBy] = $row[$this->groupBy] ?? null;
                        }
                    }
                }
            }

        }
        if (isset($this->groupBy)) {
            return $this->groupData($result);
        }
        return array_values($result);
    }

    protected function validateColumns(): void
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

    protected function rowMeetsCriteria(array $row): bool
    {
        foreach ($row as $column => $value) {
            if (isset($this->criteria[$column])) {
                foreach ($this->criteria[$column] as $criterion) {
                    if (!$criterion($value, $row)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    protected function groupData(array $data): array
    {
        if (!isset($this->groupBy)) {
            return $data;
        }
        $grouped = [];
        foreach ($data as $row) {
            $key = $row[$this->groupBy] ?? 0;
            unset($row[$this->groupBy]);
            $grouped[$key][] = $row;
        }
        return $grouped;
    }
}