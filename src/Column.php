<?php


namespace Componentous\ArrayQuery;


use Exception;

class Column implements ColumnInterface
{
    protected string $name;
    protected string $type;
    protected bool $notNull;
    /** @var mixed|null  */
    protected $defaultValue = null;

    /**
     * Column constructor.
     * @param string $name
     * @param string $type
     * @param bool $notNull
     * @param mixed|null $defaultValue
     */
    public function __construct(string $name, string $type, bool $notNull = false, $defaultValue = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->notNull = $notNull;
        $this->defaultValue = $defaultValue;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getNotNull(): bool
    {
        return $this->notNull;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function handleNull() {
        if ($this->notNull && $this->defaultValue == null) {
            throw new Exception("Column '$this->name' cannot be null");
        }
        return $this->defaultValue;
    }
}
