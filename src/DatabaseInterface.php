<?php

namespace Componentous\ArrayQuery;

interface DatabaseInterface
{
    public function addTable(string $name, array $array): bool;

    public function hasTable(string $name): bool;

    public function hasColumn(string $column): array;

    public function tableHasColumn(string $table, string $column): bool;

    public function getTable(string $name): ?array;

    public function dropTable(string $name): bool;
}
