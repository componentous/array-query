<?php

namespace Componentous\ArrayQuery;

use Exception;

interface ColumnInterface
{
    public function getName(): string;

    public function getType(): string;

    public function getNotNull(): bool;

    public function getDefaultValue();

    /**
     * @return mixed|null
     * @throws Exception
     */
    public function handleNull();
}