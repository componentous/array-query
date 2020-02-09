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
        $result = $this->db->add('something', []);
        $this->assertSame(true, $result);
    }

    public function testAddReturnsFalseWhenTryingToAddAnExistingName()
    {
        $this->db->add('something', []);
        $result = $this->db->add('something', ['trying to add the same name']);
        $this->assertSame(false, $result);
    }

    public function testGet()
    {
        $this->db->add('something', ['array we added']);
        $result = $this->db->get('something');
        $this->assertSame(['array we added'], $result);
    }

    public function testGetReturnsNullIfNameDoesNotExist()
    {
        $result = $this->db->get('nonexistent');
        $this->assertNull($result);
    }

    public function testRemove()
    {
        $this->db->add('something', []);
        $result = $this->db->remove('something');
        $this->assertSame(true, $result);
        $isItGone = $this->db->get('something');
        $this->assertNull($isItGone);
    }

    public function testRemoveReturnsFalseIfNameDoesNotExist()
    {
        $result = $this->db->remove('nonexistent');
        $this->assertSame(false, $result);
    }

    public function testHasTable()
    {
        $tryBeforeItExists = $this->db->hasTable('nonexistent');
        $this->assertSame(false, $tryBeforeItExists);
        $this->db->add('aTable', ['a value']);
        $tryAfterItExists = $this->db->hasTable('aTable');
        $this->assertSame(true, $tryAfterItExists);
    }

    public function testTableHasColumn()
    {
        $tryWithoutTable = $this->db->tableHasColumn('nonexistentTable', 'nonexistentColumn');
        $this->assertSame(false, $tryWithoutTable);
        $this->db->add('noColumns', []);
        $tryWithoutColumn = $this->db->tableHasColumn('noColumns', 'nonexistentColumn');
        $this->assertSame(false, $tryWithoutColumn);
        $this->db->add('withColumn', [['column' => 'value']]);
        $tryWithColumn = $this->db->tableHasColumn('withColumn', 'column');
        $this->assertSame(true, $tryWithColumn);
    }
    public function testHasColumn()
    {
        $tryWithoutTable = $this->db->hasColumn('nonexistentColumn');
        $this->assertSame([], $tryWithoutTable);
        $this->db->add('noColumns', []);
        $tryWithoutColumn = $this->db->hasColumn('nonexistentColumn');
        $this->assertSame([], $tryWithoutColumn);
        $this->db->add('withColumn', [['column' => 'value']]);
        $tryWithColumn = $this->db->hasColumn('column');
        $this->assertSame(['withColumn'], $tryWithColumn);
        $this->db->add('hasSameColumn', [['column' => 'value']]);
        $tryWithMultipleInstances = $this->db->hasColumn('column');
        $this->assertSame(['withColumn', 'hasSameColumn'], $tryWithMultipleInstances);
    }
}
