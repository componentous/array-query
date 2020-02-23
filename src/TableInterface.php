<?php

namespace Componentous\ArrayQuery;

use Exception;

interface TableInterface
{
    /**
     * @param mixed[] $data
     * @return void
     * @throws Exception
     */
    public function initFromData(array $data): void;

    /**
     * @param mixed[] $data
     * @return $this
     * @throws Exception
     */
    public function fillData(array $data): self;

    /**
     * @param array $row
     * @return bool
     * @throws Exception
     */
    public function insert(array $row): bool;

    /**
     * @param array $row
     * @param ColumnInterface $column
     * @return mixed|null
     * @throws Exception
     */
    public function getValue(array $row, ColumnInterface $column);

    /**
     * @param string $name
     * @return bool
     */
    public function hasColumn(string $name): bool;

    /**
     * @param ColumnInterface $column
     * @return $this
     * @throws Exception
     */
    public function addColumn(ColumnInterface $column): self;

    /**
     * @param string $name
     * @param string $type
     * @param bool $notNull
     * @param mixed $defaultValue
     * @return $this
     * @throws Exception
     */
    public function createColumn(string $name, string $type, bool $notNull = false, $defaultValue): self;

    /**
     * @param string $name
     * @return ColumnInterface
     * @throws Exception
     */
    public function getColumnDefinition(string $name): ColumnInterface;

    /**
     * @param string $name
     * @return array
     * @throws Exception
     */
    public function getColumnData(string $name): array;

    /**
     * @param string $name
     * @return $this
     * @throws Exception
     */
    public function dropColumn(string $name): self;

    /**
     * @param mixed[] $data
     * @return ColumnInterface[]
     * @throws Exception
     */
    public function defineColumnsFromData(array $data): array;

    /**
     * @param string $name
     * @param string[] $types
     * @param int $rowCount
     * @return ColumnInterface
     */
    public function defineColumnFromTypes(string $name, array $types, int $rowCount): ColumnInterface;

    /**
     * @param mixed[] $data
     * @return string[]
     */
    public function findColumnTypes(array $data): array;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return ColumnInterface[]
     */
    public function getColumns(): array;

    /**
     * @return array
     */
    public function getData(): array;
}