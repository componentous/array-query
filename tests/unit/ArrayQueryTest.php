<?php


use Componentous\ArrayQuery\ArrayQuery;
use Componentous\ArrayQuery\ArrayDb;
use PHPUnit\Framework\TestCase;


class ArrayQueryTest extends TestCase
{
    protected ArrayQuery $query;
    protected array $exampleArray = [
        ['column1' => 'val1A', 'column2' => 'val2A', 'column3' => 'val3A'],
        ['column1' => 'val1B', 'column2' => 'val2B'],
        ['column1' => 'val1C', 'column2' => 'val2C', 'column4' => 'val4C'],
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->query = new ArrayQuery(new ArrayDb());
    }

    public function testSelect()
    {
        $this->query->getDb()->add('anArray', $this->exampleArray);
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
        $this->query->getDb()->add('oneTable', [['column' => 'value']]);
        $this->query->getDb()->add('anotherTable', [['column' => 'value']]);
        $this->query->select('column');
    }

    public function testFrom()
    {
        $this->query->getDb()->add('anArray', $this->exampleArray);
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
        $this->query->getDb()->add('aTable', $this->exampleArray);
        $this->query->select('column1');
        $this->query->getResult();
    }

    public function testGetResultCanSelectAColumn()
    {
        $this->query->getDb()->add('table', $this->exampleArray);
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
        $this->query->getDb()->add('table', $this->exampleArray);
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
        $this->query->getDb()->add('table', $this->exampleArray);
        $result = $this->query->select('column3', 'column1', 'column4')->from('table')->getResult();
        $expected = [
            ['column3' => 'val3A', 'column1' => 'val1A', 'column4' => null],
            ['column3' => null,    'column1' => 'val1B', 'column4' => null],
            ['column3' => null,    'column1' => 'val1C', 'column4' => 'val4C']
        ];
        $this->assertSame($expected, $result);
    }

    public function testSomething()
    {
        $this->query->getDb()->add('table', $this->exampleArray);
        $result = $this->query->select('column1')
            ->from('table')
            ->where('column2', fn($val) => $val != 'val2B')
            ->getResult();
        $this->assertTrue(true);
        $expected = [
            ['column1' => 'val1A'],
            ['column1' => 'val1C']
        ];
        $this->assertSame($expected, $result);
    }
}
