<?php

namespace unit;

use Componentous\ArrayQuery\Column;
use Exception;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    /**
     * @covers \Componentous\ArrayQuery\Column::getName()
     */
    public function testGetName()
    {
        $column = new Column('test', 'string');
        $name = $column->getName();
        $this->assertSame('test', $name);
    }

    /**
     * @covers \Componentous\ArrayQuery\Column::getType()
     */
    public function testGetType()
    {
        $column = new Column('test', 'string');
        $type = $column->getType();
        $this->assertSame('string', $type);
    }

    /**
     * @covers \Componentous\ArrayQuery\Column::getNotNull()
     */
    public function testGetNotNull()
    {
        $column = new Column('test', 'string', true);
        $notNull = $column->getNotNull();
        $this->assertSame(true, $notNull);
    }

    /**
     * @covers \Componentous\ArrayQuery\Column::getNotNull()
     */
    public function testNotNullDefaultsToFalse()
    {
        $column = new Column('test', 'string');
        $notNull = $column->getNotNull();
        $this->assertSame(false, $notNull);
    }

    /**
     * @covers \Componentous\ArrayQuery\Column::getDefaultValue()
     */
    public function testGetDefaultValue()
    {
        $column = new Column('test', 'string', true, 'something');
        $default = $column->getDefaultValue();
        $this->assertSame('something', $default);
    }

    /**
     * @covers \Componentous\ArrayQuery\Column::getDefaultValue()
     */
    public function testDefaultDefaultValueIsNull()
    {
        $column = new Column('test', 'string');
        $default = $column->getDefaultValue();
        $this->assertNull($default);
    }

    /**
     * @covers \Componentous\ArrayQuery\Column::handleNull()
     * @throws Exception
     */
    public function testHandleNullReturnsNullByDefault()
    {
        $column = new Column('test', 'string');
        $value = $column->handleNull();
        $this->assertNull($value);
    }

    /**
     * @covers \Componentous\ArrayQuery\Column::handleNull()
     * @throws Exception
     */
    public function testHandleNullReturnsDefaultValue()
    {
        $column = new Column('test', 'string', true, 'something');
        $value = $column->handleNull();
        $this->assertSame('something', $value);
        $column = new Column('test', 'string', false, 'something');
        $value = $column->handleNull();
        $this->assertSame('something', $value);
    }

    /**
     * @covers \Componentous\ArrayQuery\Column::handleNull()
     * @throws Exception
     */
    public function testHandleNullThrowsExceptionIfNotNullIsTrueAndDefaultValueIsNull()
    {
        $column = new Column('test', 'string', true, null);
        $this->expectException('Exception');
        $column->handleNull();
    }
}
