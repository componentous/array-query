<?php


use Componentous\ArrayQuery\Query;
use Componentous\ArrayQuery\Database;
use PHPUnit\Framework\TestCase;


class QueryTest extends TestCase
{
    protected Query $query;
    protected array $exampleArray1 = [
        ['column1' => 'val1A', 'column2' => 'val2A', 'column3' => 'val3A'],
        ['column1' => 'val1B', 'column2' => 'val2B'],
        ['column1' => 'val1C', 'column2' => 'val2C', 'column4' => 'val4C'],
    ];
    protected array $exampleArray2 = [
        ['id' => 1, 'name' => 'betty'],
        ['id' => 2, 'name' => 'al'],
        ['id' => 3, 'name' => 'betty'],
        ['id' => 4, 'name' => 'al'],
        ['id' => 5, 'name' => 'al'],
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->query = new Query(new Database());
    }

    public function testSelect()
    {
        $this->query->getDb()->addTable('anArray', $this->exampleArray1);
        $selfForChaining = $this->query->select('column1', 'column2');
        $this->assertSame($this->query, $selfForChaining);
        $this->assertSame(['anArray' =>['column1', 'column2']], $this->query->getColumns());
    }

    public function testSelectThrowsExceptionForNonexistentColumn()
    {
        $this->expectException('InvalidArgumentException');
        $this->query->select('nonexistent');
    }

    public function testSelectThrowsExceptionForAmbiguousColumn()
    {
        $this->expectException('InvalidArgumentException');
        $this->query->getDb()->addTable('oneTable', [['column' => 'value']]);
        $this->query->getDb()->addTable('anotherTable', [['column' => 'value']]);
        $this->query->select('column');
    }

    public function testFrom()
    {
        $this->query->getDb()->addTable('anArray', $this->exampleArray1);
        $selfForChaining = $this->query->from('anArray');
        $this->assertSame($this->query, $selfForChaining);
        $this->assertSame(['anArray' => true], $this->query->getTables());
        $this->assertSame('anArray', $this->query->getTable());
    }

    public function testFromThrowsExceptionForNonexistentTable()
    {
        $this->expectException('InvalidArgumentException');
        $this->query->from('nonexistentTable');
    }

    public function testGetResultThrowsAnExceptionIfColumnTablesAreNotInFromClause()
    {
        $this->expectException('RuntimeException');
        $this->query->getDb()->addTable('aTable', $this->exampleArray1);
        $this->query->select('column1');
        $this->query->getResult();
    }

    public function testGetResultCanSelectAColumn()
    {
        $this->query->getDb()->addTable('table', $this->exampleArray1);
        $result = $this->query->select('column1')->from('table')->getResult();
        $expected = [
            ['column1' => 'val1A'],
            ['column1' => 'val1B'],
            ['column1' => 'val1C'],
        ];
        $this->assertSame($expected, $result);
    }

    public function testGetResultFillsNullsForMissingKeys()
    {
        $this->query->getDb()->addTable('table', $this->exampleArray1);
        $result = $this->query->select('column3')->from('table')->getResult();
        $expected = [
            ['column3' => 'val3A'],
            ['column3' => null],
            ['column3' => null]
        ];
        $this->assertSame($expected, $result);
    }

    public function testGetResultSelectsMultipleColumnsInSpecifiedOrder()
    {
        $this->query->getDb()->addTable('table', $this->exampleArray1);
        $result = $this->query->select('column3', 'column1', 'column4')->from('table')->getResult();
        $expected = [
            ['column3' => 'val3A', 'column1' => 'val1A', 'column4' => null],
            ['column3' => null,    'column1' => 'val1B', 'column4' => null],
            ['column3' => null,    'column1' => 'val1C', 'column4' => 'val4C']
        ];
        $this->assertSame($expected, $result);
    }

    public function testWhere()
    {
        $this->query->getDb()->addTable('table', $this->exampleArray1);
        $result = $this->query->select('column1')
            ->from('table')
            ->where('column2', fn($val) => $val != 'val2B')
            ->getResult();
        $expected = [
            ['column1' => 'val1A'],
            ['column1' => 'val1C']
        ];
        $this->assertSame($expected, $result);
    }

    public function testGroupBy()
    {
        $this->query->getDb()->addTable('table', $this->exampleArray2);
        $result = $this->query->select('id')
            ->from('table')
            ->groupBy('name')
            ->getResult();
        $expected = [
            'betty' => [['id' => 1], ['id' => 3]],
            'al' => [['id' => 2], ['id' => 4], ['id' => 5]]
        ];
        $this->assertSame($expected, $result);
    }
}
