<?php

namespace Componentous\ArrayQuery;

interface QueryInterface
{
    public function getTable(): string;

    public function getTables(): array;

    public function getColumns(): array;

    public function getDb(): DatabaseInterface;

    public function select(string ...$columns): self;

    public function from(string $table): self;

    public function where(string $column, callable $criterion): self;

    public function groupBy(string $column): self;

    public function getResult(): array;
}