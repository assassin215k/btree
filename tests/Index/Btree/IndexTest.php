<?php
/**
 * Created by PhpStorm.
 * Author: Ihor Fedan
 * Date: 26.01.22
 * Time: 22:27
 */

namespace Btree\Test\Index\Btree;

use Btree\Exception\MissedFieldException;
use Btree\Index\Btree\Index;
use Btree\Index\Btree\Node\NodeInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

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
        $this->expectException(MissedFieldException::class);
        $index = new Index([]);

        $this->expectException(MissedFieldException::class);
        $index = new Index('');

        $this->expectException(MissedFieldException::class);
        $index = new Index(['asd', '']);

        $index = new Index('asd');
        $this->assertSame(100, $index::$nodeSize);

        Index::$nodeSize = 3;
        $index = new Index('asd');
        $this->assertSame(3, $index::$nodeSize);
    }

    public function testInsert()
    {
        Index::$nodeSize = 3;
        $index = new Index(['name', 'age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }

        $this->assertTrue(true);
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
