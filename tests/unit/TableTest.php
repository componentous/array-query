<?php

namespace unit;

use Componentous\ArrayQuery\Table;
use Componentous\ArrayQuery\Column;
use Exception;
use PHPUnit\Framework\TestCase;
use DateTime;

class TableTest extends TestCase
{
    protected Table $table;
    protected array $inputExample;
    protected array $inputExampleWithNulls;

    protected function setUp(): void
    {
        $this->table = new Table('test');
        $this->inputExample = [
            ['a', 1, new DateTime(), 3.14],
            ['b', 2, new DateTime(), 1.23],
            ['c', 3, new DateTime(), 4.56]
        ];
        $this->inputExampleWithNulls = [
            [null, 1,    new DateTime(), 3.14],
            ['b',  2,    null,           null],
            ['c',  null, new DateTime(), 4.56]
        ];
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::getName()
     */
    public function testGetName()
    {
        $name = $this->table->getName();
        $this->assertSame('test', $name);
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::getColumns()
     */
    public function testGetColumns()
    {
        $columns = $this->table->getColumns();
        $this->assertSame([], $columns);
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::createColumn()
     * @throws Exception
     */
    public function testCreateColumn()
    {
        $this->table->createColumn('test', 'string');
        $columns = $this->table->getColumns();
        $this->assertCount(1, $columns);
        $column = reset($columns);
        $this->assertEquals(new Column('test', 'string'), $column);
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::createColumn()
     * @covers \Componentous\ArrayQuery\Table::validateColumn()
     * @throws Exception
     */
    public function testCreateColumnThrowsExceptionForExistingColumn()
    {
        $this->expectException('Exception');
        $this->table->createColumn('test', 'string');
        $this->table->createColumn('test', 'integer');
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::addColumn()
     * @throws Exception
     */
    public function testAddColumn()
    {
        $this->table->addColumn(new Column('test', 'string'));
        $columns = $this->table->getColumns();
        $this->assertCount(1, $columns);
        $column = reset($columns);
        $this->assertEquals(new Column('test', 'string'), $column);
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::addColumn()
     * @covers \Componentous\ArrayQuery\Table::validateColumn()
     * @throws Exception
     */
    public function testAddColumnThrowsExceptionForExistingColumn()
    {
        $this->expectException('Exception');
        $this->table->addColumn(new Column('test', 'string'));
        $this->table->addColumn(new Column('test', 'string'));
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::getData()
     */
    public function testGetData()
    {
        $data = $this->table->getData();
        $this->assertSame([], $data);
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::findColumnTypes()
     */
    public function testFindColumnTypes()
    {
        $types = $this->table->findColumnTypes($this->inputExample);
        $expected = [
            ['string' => 3],
            ['integer' => 3],
            ['DateTime' => 3],
            ['double' => 3]
        ];
        $this->assertSame($expected, $types);
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::defineColumnsFromData()
     * @throws Exception
     */
    public function testDefineColumnsFromData()
    {
        $columns = $this->table->defineColumnsFromData($this->inputExample);
        $expected = [
            new Column('0', 'string', true),
            new Column('1', 'integer', true),
            new Column('2', 'DateTime', true),
            new Column('3', 'double', true)
        ];
        $this->assertEquals($expected, $columns);
        $columns = $this->table->defineColumnsFromData($this->inputExampleWithNulls);
        $expected = [
            new Column('0', 'string', false),
            new Column('1', 'integer', false),
            new Column('2', 'DateTime', false),
            new Column('3', 'double', false)
        ];
        $this->assertEquals($expected, $columns);
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::hasColumn()
     * @throws Exception
     */
    public function testHasColumn()
    {
        $this->assertTrue(true);
        $hasColumn = $this->table->hasColumn('test');
        $this->assertFalse($hasColumn);
        $this->table->addColumn(new Column('test', 'string'));
        $hasColumn = $this->table->hasColumn('test');
        $this->assertTrue($hasColumn);
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::dropColumn()
     * @throws Exception
     */
    public function testDropColumn()
    {
        $column = new Column('test', 'string');
        $this->table->addColumn($column);
        $columns = $this->table->getColumns();
        $this->assertEquals(['test' => $column], $columns);
        $this->table->dropColumn('test');
        $columns = $this->table->getColumns();
        $this->assertEquals([], $columns);
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::dropColumn()
     * @covers \Componentous\ArrayQuery\Table::validateColumn()
     * @throws Exception
     */
    public function testDropColumnThrowsExceptionForNonexistentColumn()
    {
        $this->expectException('Exception');
        $this->table->dropColumn('nonexistent');
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::defineColumnFromTypes()
     * @throws Exception
     */
    public function testDefineColumnFromTypesDefaultsToStringTypeForEmptyType()
    {
        $column = $this->table->defineColumnFromTypes('test', ['NULL' => 1], 1);
        $expected = new Column('test', 'string', false);
        $this->assertEquals($expected, $column);
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::defineColumnFromTypes()
     * @throws Exception
     */
    public function testDefineColumnsFromTypesThrowsExceptionForAmbiguousTypes()
    {
        $this->expectException('Exception');
        $this->table->defineColumnFromTypes('test', ['string' => 1, 'integer' => 1], 2);
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::getColumnDefinition()
     * @throws Exception
     */
    public function testGetColumnDefinition()
    {
        $columnIn = new Column('test', 'string');
        $this->table->addColumn($columnIn);
        $columnOut = $this->table->getColumnDefinition('test');
        $this->assertEquals($columnIn, $columnOut);
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::getColumnDefinition()
     * @throws Exception
     */
    public function testGetColumnDefinitionThrowsExceptionForUnknownColumn()
    {
        $this->expectException('Exception');
        $this->table->getColumnDefinition('nonexistent');
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::insert()
     * @throws Exception
     */
    public function testInsert()
    {
        $this->table
            ->addColumn(new Column('a', 'integer'))
            ->addColumn(new Column('b', 'string'));
        $row = ['a' => 1, 'b' => 'one'];
        $this->table->insert($row);
        $this->assertEquals($this->table->getData(), [$row]);
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::fillData()
     * @throws Exception
     */
    public function testFillData()
    {
        $this->table
            ->addColumn(new Column('a', 'integer'))
            ->addColumn(new Column('b', 'string'));
        $data = [
            ['a' => 1, 'b' => 'one'],
            ['a' => 2, 'b' => 'two'],
        ];
        $this->table->fillData($data);
        $this->assertEquals($this->table->getData(), $data);
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::initFromData()
     * @throws Exception
     */
    public function testInitFromData()
    {
        $data = [
            ['a' => 1, 'b' => 'one'],
            ['a' => 2, 'b' => 'two'],
        ];
        $this->table->initFromData($data);
        $this->assertEquals($this->table->getData(), $data);
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::getValue()
     * @throws Exception
     */
    public function testGetValue()
    {
        $columnA = new Column('test', 'string');
        $valueA = $this->table->getValue(['test' => 'something'], $columnA);
        $this->assertEquals('something', $valueA);
        $valueB = $this->table->getValue(['test' => null], $columnA);
        $this->assertEquals(null, $valueB);
        $columnB = new Column('test', 'string', true, 'default');
        $valueC = $this->table->getValue(['test' => null], $columnB);
        $this->assertEquals('default', $valueC);
        $columnC = new Column('test', 'string', false, 'default');
        $valueD = $this->table->getValue(['test' => null], $columnC);
        $this->assertEquals('default', $valueD);
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::getColumnData()
     * @throws Exception
     */
    public function testGetColumnData()
    {
        $data = [
            ['a' => 1, 'b' => 'one'],
            ['a' => 2, 'b' => 'two'],
        ];
        $this->table->initFromData($data);
        $column = $this->table->getColumnData('a');
        $this->assertEquals([1, 2], $column);
    }

    /**
     * @covers \Componentous\ArrayQuery\Table::__construct()
     * @throws Exception
     */
    public function testTableConstructsFromData()
    {
        $data = [
            ['a' => 1, 'b' => 'one'],
            ['a' => 2, 'b' => 'two'],
        ];
        $table = new Table('test', $data);
        $expectedColumns = [
            'a' => new Column('a', 'integer', true),
            'b' => new Column('b', 'string', true)
        ];
        $this->assertEquals($expectedColumns, $table->getColumns());
        $this->assertEquals($data, $table->getData());
    }

    public function testDropColumnRemovesData()
    {
        $data = [
            ['a' => 1, 'b' => 'one'],
            ['a' => 2, 'b' => 'two'],
        ];
        $table = new Table('test', $data);
        $table->dropColumn('a');
        $expectedColumns = [
            'b' => new Column('b', 'string', true)
        ];
        $this->assertEquals($expectedColumns, $table->getColumns());
        $this->assertEquals([
            ['b' => 'one'],
            ['b' => 'two']
        ], $table->getData());
    }
}