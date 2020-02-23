<?php

namespace unit;

use Componentous\ArrayQuery\Database;
use Componentous\ArrayQuery\Table;
use PHPUnit\Framework\TestCase;

class ArrayDbTest extends TestCase
{
    protected Database $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = new Database();
    }

    /**
     * @covers \Componentous\ArrayQuery\Database::addTable()
     */
    public function testAddTable()
    {
        $result = $this->db->addTable('something', []);
        $this->assertSame(true, $result);
    }

    /**
     * @covers \Componentous\ArrayQuery\Database::addTable()
     */
    public function testAddTableReturnsFalseWhenTryingToAddAnExistingName()
    {
        $this->db->addTable('something', []);
        $result = $this->db->addTable('something', ['trying to add the same name']);
        $this->assertSame(false, $result);
    }

    /**
     * @covers \Componentous\ArrayQuery\Database::getTable()
     */
    public function testGetTable()
    {
        $this->db->addTable('something', [['column' => 'value']]);
        $result = $this->db->getTable('something');
        $this->assertInstanceOf(Table::class, $result);
    }

    /**
     * @covers \Componentous\ArrayQuery\Database::getTable()
     */
    public function testGetTableReturnsNullIfNameDoesNotExist()
    {
        $result = $this->db->getTable('nonexistent');
        $this->assertNull($result);
    }

    /**
     * @covers \Componentous\ArrayQuery\Database::dropTable()
     */
    public function testDropTable()
    {
        $this->db->addTable('something', []);
        $result = $this->db->dropTable('something');
        $this->assertSame(true, $result);
        $isItGone = $this->db->getTable('something');
        $this->assertNull($isItGone);
    }

    /**
     * @covers \Componentous\ArrayQuery\Database::dropTable()
     */
    public function testRemoveReturnsFalseIfNameDoesNotExist()
    {
        $result = $this->db->dropTable('nonexistent');
        $this->assertSame(false, $result);
    }

    /**
     * @covers \Componentous\ArrayQuery\Database::hasTable()
     */
    public function testHasTable()
    {
        $tryBeforeItExists = $this->db->hasTable('nonexistent');
        $this->assertSame(false, $tryBeforeItExists);
        $this->db->addTable('aTable', [['column' => 'value']]);
        $tryAfterItExists = $this->db->hasTable('aTable');
        $this->assertSame(true, $tryAfterItExists);
    }

    /**
     * @covers \Componentous\ArrayQuery\Database::tableHasColumn()
     */
    public function testTableHasColumn()
    {
        $tryWithoutTable = $this->db->tableHasColumn('nonexistentTable', 'nonexistentColumn');
        $this->assertSame(false, $tryWithoutTable);
        $this->db->addTable('noColumns', []);
        $tryWithoutColumn = $this->db->tableHasColumn('noColumns', 'nonexistentColumn');
        $this->assertSame(false, $tryWithoutColumn);
        $this->db->addTable('withColumn', [['column' => 'value']]);
        $tryWithColumn = $this->db->tableHasColumn('withColumn', 'column');
        $this->assertSame(true, $tryWithColumn);
    }

    /**
     * @covers \Componentous\ArrayQuery\Database::hasColumn()
     */
    public function testHasColumn()
    {
        $tryWithoutTable = $this->db->hasColumn('nonexistentColumn');
        $this->assertSame([], $tryWithoutTable);
        $this->db->addTable('noColumns', []);
        $tryWithoutColumn = $this->db->hasColumn('nonexistentColumn');
        $this->assertSame([], $tryWithoutColumn);
        $this->db->addTable('withColumn', [['column' => 'value']]);
        $tryWithColumn = $this->db->hasColumn('column');
        $this->assertSame(['withColumn'], $tryWithColumn);
        $this->db->addTable('hasSameColumn', [['column' => 'value']]);
        $tryWithMultipleInstances = $this->db->hasColumn('column');
        $this->assertSame(['withColumn', 'hasSameColumn'], $tryWithMultipleInstances);
    }
}
