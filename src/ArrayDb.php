<?php


namespace Componentous\ArrayQuery;


class ArrayDb
{
    protected array $arrays = [];

    public function add(string $name, array $array): bool
    {
        if (isset($this->arrays[$name])) {
            return false;
        } else {
            $this->arrays[$name] = $array;
            return true;
        }
    }

    public function get(string $name): ?array
    {
        return $this->arrays[$name] ?? null;
    }

    public function remove(string $name): bool
    {
        if (isset($this->arrays[$name])) {
            unset($this->arrays[$name]);
            return true;
        } else {
            return false;
        }
    }
}