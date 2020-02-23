<?php


namespace Componentous\ArrayQuery;


use Exception;

class Table implements TableInterface
{
    protected string $name;
    /** @var ColumnInterface[] */
    protected array $columns = [];
    /** @var array[] */
    protected array $data = [];

    /**
     * Table constructor.
     * @param string $name
     * @param mixed[] $data
     * @throws Exception
     */
    public function __construct(string $name, array $data = [])
    {
        $this->name = $name;
        if ($data) {
            $this->initFromData($data);
        }
    }

    public function initFromData(array $data): void
    {
        $this->columns = $this->defineColumnsFromData($data);
        $this->fillData($data);
    }

    public function fillData(array $data): TableInterface
    {
        $this->data = [];
        foreach ($data as $row) {
            $this->insert($row);
        }
        return $this;
    }

    public function insert(array $row): bool
    {
        $tableRow = [];
        foreach ($this->columns as $name => $column) {
            $tableRow[$name] = $this->getValue($row, $column);
        }
        $this->data[] = $tableRow;
        return true;
    }

    public function getValue(array $row, ColumnInterface $column)
    {
        $name = $column->getName();
        if (isset($row[$name])) {
            return $row[$name];
        } else {
            return $column->handleNull();
        }
    }

    public function hasColumn(string $name): bool
    {
        return isset($this->columns[$name]);
    }

    /**
     * @param string $name
     * @param bool $absent
     * @return void
     * @throws Exception
     */
    protected function validateColumn(string $name, $absent = false): void
    {
        if ($absent && isset($this->columns[$name])) {
            throw new Exception("Column '$name' exists");
        }
        if (!$absent && !isset($this->columns[$name])) {
            throw new Exception("Column '$name' does not exist");
        }
    }

    public function addColumn(ColumnInterface $column): self
    {
        $name = $column->getName();
        $this->validateColumn($name, true);
        $this->columns[$name] = $column;
        return $this;
    }

    public function createColumn(string $name, string $type, bool $notNull = false, $defaultValue = null): self
    {
        $this->validateColumn($name, true);
        $this->columns[$name] = new Column($name, $type, $notNull, $defaultValue);
        return $this;
    }

    public function getColumnDefinition(string $name): Column
    {
        $this->validateColumn($name);
        return $this->columns[$name];
    }

    public function getColumnData(string $name): array
    {
        $this->validateColumn($name);
        return array_column($this->data, $name);
    }

    public function dropColumn(string $name): self
    {
        $this->validateColumn($name);
        unset($this->columns[$name]);
        $column = [$name => 1];
        $this->data = array_map(fn($row) => array_diff_key($row, $column), $this->data);
        return $this;
    }

    public function defineColumnsFromData(array $data): array
    {
        $columnTypes = $this->findColumnTypes($data);
        $columns = [];
        $rowCount = count($data);
        foreach ($columnTypes as $name => $types) {
            $columns[$name] = $this->defineColumnFromTypes($name, $types, $rowCount);
        }
        return $columns;
    }

    public function defineColumnFromTypes(string $name, array $types, int $rowCount): ColumnInterface
    {
        $notNull = !isset($types['NULL']) && array_sum($types) == $rowCount;
        unset($types['NULL']);
        if (!$types) {
            $types = ['string' => 1];
        }
        if (count($types) == 1) {
            return new Column($name, key($types), $notNull);
        }
        throw new Exception("Ambiguous types for column '$name': " . implode(', ', $types));
    }

    public function findColumnTypes(array $data): array
    {
        $columns = [];
        foreach ($data as $row) {
            foreach ($row as $column => $value) {
                $type = is_object($value) ? get_class($value) : gettype($value);
                $columns[$column][$type] = 1 + ($columns[$column][$type] ?? 0);
            }
        }
        return $columns;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getData(): array
    {
        return $this->data;
    }
}