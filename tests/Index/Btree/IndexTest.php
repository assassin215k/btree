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

        $print = "
_____
__________N<Owen17
__________N>Owen17
__________N<Artur28
__________N>Artur28
";

        $this->assertSame($print, $index->printTree());

        $this->expectException(MissedPropertyException::class);
        $index->insert(new \DateTime());
        //todo added check via search
    }

    public function testInsertLarge()
    {
        Index::$nodeSize = 10;
        $data = [];
        for ($i = 5; $i < 1000; $i++) {
            $data[] = new Person('User', $i);
        }

        $index = new Index(['name', 'age']);
        foreach ($data as $person) {
            $index->insert($person);
        }

        $print = "
_____
__________N<User822
_______________N<User990
_______________N>User990
_______________N>User981
_______________N>User972
_______________N>User963
_______________N>User954
_______________N>User945
_______________N>User936
_______________N>User927
_______________N>User918
_______________N>User909
_______________N<User886
_______________N>User886
_______________N>User877
_______________N>User868
_______________N>User859
_______________N<User840
_______________N>User840
_______________N>User831
__________N>User822
_______________N>User822
_______________N>User813
_______________N>User804
_______________N>User796
_______________N>User787
_______________N>User778
_______________N>User769
_______________N<User750
_______________N>User750
_______________N>User741
__________N>User732
_______________N>User732
_______________N>User723
_______________N>User714
_______________N>User705
_______________N>User697
_______________N>User688
_______________N>User679
_______________N<User660
_______________N>User660
_______________N>User651
__________N>User642
_______________N>User642
_______________N>User633
_______________N>User624
_______________N>User615
_______________N>User606
_______________N>User598
_______________N>User589
_______________N<User570
_______________N>User570
_______________N>User561
__________N>User552
_______________N>User552
_______________N>User543
_______________N>User534
_______________N>User525
_______________N>User516
_______________N>User507
_______________N>User499
_______________N<User480
_______________N>User480
_______________N>User471
__________N>User462
_______________N>User462
_______________N>User453
_______________N>User444
_______________N>User435
_______________N>User426
_______________N>User417
_______________N>User408
_______________N>User399
_______________N<User380
_______________N>User380
__________N>User371
_______________N>User371
_______________N>User362
_______________N>User353
_______________N>User344
_______________N>User335
_______________N>User326
_______________N>User317
_______________N>User308
_______________N>User299
_______________N<User280
__________N>User280
_______________N>User280
_______________N>User271
_______________N>User262
_______________N>User253
_______________N>User244
_______________N>User235
_______________N>User226
_______________N>User217
_______________N>User208
_______________N>User199
__________N>User19
_______________N<User180
_______________N>User180
_______________N>User171
_______________N>User162
_______________N>User153
_______________N>User144
_______________N>User135
_______________N>User126
_______________N>User117
_______________N>User108
";

        $this->assertSame($print, $index->printTree());
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
