<?php

namespace Btree\Test\Index\Btree;

use Btree\Exception\MissedFieldException;
use Btree\Exception\MissedPropertyException;
use Btree\Index\Btree\Index;
use PHPUnit\Framework\TestCase;

/**
 * Test of Index Class
 *
 * @package assassin215k/btree
 */
class IndexTest extends TestCase
{
    private array $data;

    public function setUp(): void
    {
        $this->data = [
            new Person('Olga', 28),
            new Person('Owen', 17),
            new Person('Lisa', 44),
            new Person('Alex', 31),
            new Person('Artur', 28),
            new Person('Ivan', 17),
            new Person('Roman', 44),
            new Person('Peter', 31),
            new Person('Olga', 18),
            new Person('Owen', 27),
            new Person('Lisa', 34),
            new Person('Alex', 21),
        ];
    }

    public function testConstruct()
    {
        $index = new Index('asd');
        $this->assertSame(100, $index::$nodeSize);

        Index::$nodeSize = 3;
        $index = new Index('asd');
        $this->assertSame(3, $index::$nodeSize);
    }

    public function testConstructMissedFields()
    {
        $this->expectException(MissedFieldException::class);
        new Index([]);
    }

    public function testConstructEmptyField()
    {
        $this->expectException(MissedFieldException::class);
        new Index('');
    }

    public function testConstructEmptyFields()
    {
        $this->expectException(MissedFieldException::class);
        new Index(['asd', '']);
    }

    public function testInsert()
    {
        Index::$nodeSize = 3;
        $index = new Index(['name', 'age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }

        $index->insert($this->data[0]);

        $this->expectException(MissedPropertyException::class);
        $index->insert(new \DateTime());

        //todo added check via search
    }
//
//    public function testPrintTree()
//    {
//    }
//
//    public function testDelete()
//    {
//    }
//
//    public function testLessThanOrEqual()
//    {
//    }
//
//    public function testSearch()
//    {
//    }
//
//    public function testGraterThanOrEqual()
//    {
//    }
//
//    public function testLessThan()
//    {
//    }
//
//    public function testGraterThan()
//    {
//    }
//
//    public function testBetween()
//    {
//    }
}
