<?php

namespace unit;

use Componentous\ArrayQuery\ArrayDb;
use PHPUnit\Framework\TestCase;

class ArrayDbTest extends TestCase
{
    protected ArrayDb $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = new ArrayDb();
    }

    public function testAdd()
    {
        $result = $this->db->addTable('something', []);
        $this->assertSame(true, $result);
    }

    public function testAddReturnsFalseWhenTryingToAddAnExistingName()
    {
        $this->db->addTable('something', []);
        $result = $this->db->addTable('something', ['trying to add the same name']);
        $this->assertSame(false, $result);
    }

    public function testGet()
    {
        $this->db->addTable('something', ['array we added']);
        $result = $this->db->getTable('something');
        $this->assertSame(['array we added'], $result);
    }

    public function testGetReturnsNullIfNameDoesNotExist()
    {
        $result = $this->db->getTable('nonexistent');
        $this->assertNull($result);
    }

    public function testRemove()
    {
        $this->db->addTable('something', []);
        $result = $this->db->dropTable('something');
        $this->assertSame(true, $result);
        $isItGone = $this->db->getTable('something');
        $this->assertNull($isItGone);
    }

    public function testRemoveReturnsFalseIfNameDoesNotExist()
    {
        $result = $this->db->dropTable('nonexistent');
        $this->assertSame(false, $result);
    }

    public function testHasTable()
    {
        $tryBeforeItExists = $this->db->hasTable('nonexistent');
        $this->assertSame(false, $tryBeforeItExists);
        $this->db->addTable('aTable', ['a value']);
        $tryAfterItExists = $this->db->hasTable('aTable');
        $this->assertSame(true, $tryAfterItExists);
    }

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
