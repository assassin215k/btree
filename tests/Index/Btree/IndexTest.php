<?php

namespace Btree\Test\Index\Btree;

use Btree\Index\Btree\Index;
use Btree\Index\Exception\MissedFieldException;
use Btree\Index\Exception\MissedPropertyException;
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

    public function testGetFields()
    {
        $fields = ['name', 'age'];
        $index = new Index($fields);

        $this->assertSame($fields, $index->getFields());
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
__________N<User804
_______________N<User984
_______________N>User984
_______________N>User974
_______________N>User964
_______________N>User954
_______________N>User944
_______________N>User934
_______________N>User924
_______________N>User914
_______________N>User904
_______________N>User894
_______________N>User884
_______________N>User874
_______________N>User864
_______________N>User854
_______________N>User844
_______________N>User834
_______________N>User824
_______________N>User814
__________N>User804
_______________N>User804
_______________N>User794
_______________N>User784
_______________N>User774
_______________N>User764
_______________N>User754
_______________N>User744
_______________N>User734
_______________N>User724
_______________N>User714
__________N>User704
_______________N>User704
_______________N>User694
_______________N>User684
_______________N>User674
_______________N>User664
_______________N>User654
_______________N>User644
_______________N>User634
_______________N>User624
_______________N>User614
__________N>User604
_______________N>User604
_______________N>User594
_______________N>User584
_______________N>User574
_______________N>User564
_______________N>User554
_______________N>User544
_______________N>User534
_______________N>User524
_______________N>User514
__________N>User504
_______________N>User504
_______________N>User494
_______________N>User484
_______________N>User474
_______________N>User464
_______________N>User454
_______________N>User444
_______________N>User434
_______________N>User424
_______________N>User414
__________N>User404
_______________N>User404
_______________N>User394
_______________N>User384
_______________N>User374
_______________N>User364
_______________N>User354
_______________N>User344
_______________N>User334
_______________N>User324
_______________N>User314
__________N>User304
_______________N>User304
_______________N>User294
_______________N>User284
_______________N>User274
_______________N>User264
_______________N>User254
_______________N>User244
_______________N>User234
_______________N>User224
_______________N>User214
__________N>User204
_______________N>User204
_______________N>User194
_______________N>User184
_______________N>User174
_______________N>User164
_______________N>User154
_______________N>User144
_______________N>User134
_______________N>User124
_______________N>User114
__________N>User104
_______________N>User104
_______________N>User94
_______________N>User84
_______________N>User74
_______________N>User64
_______________N>User54
_______________N>User44
_______________N>User34
_______________N>User24
_______________N>User14
";

        $this->assertSame($print, $index->printTree());
    }

    public function deleteFromLeaf()
    {
        Index::$nodeSize = 3;
        $index = new Index(['name', 'age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }


        $tree = "
_____
__________N<Owen17
__________N>Owen17
__________N<Artur28
__________N>Artur28
";
        $this->assertSame($tree, $index->printTree());

        $index->delete(['name' => 'Olga', 'age' => 18]);
        $index->delete(['name' => 'Olga', 'age' => 28]);


        $tree = "
_____
__________N<Owen17
__________N>Owen17
__________N>Artur28
";
        $this->assertSame($tree, $index->printTree());


        $index->delete(['name' => 'Olga', 'age' => 28]);
        $index->delete(['name' => 'Olga', 'age' => 28]);
        $index->delete(['name' => 'Olga', 'age' => 28]);
        $tree = "
_____
__________N<Owen17
__________N>Owen17
__________N>Artur28
";
        $this->assertSame($tree, $index->printTree());


        $index->delete(['name' => 'Lisa', 'age' => 34]);
        $index->delete(['name' => 'Owen', 'age' => 27]);
        $index->delete(['name' => 'Peter', 'age' => 31]);
        $tree = "
_____
__________N<Owen17
__________N>Owen17
";
        $this->assertSame($tree, $index->printTree());


        $index->delete(['name' => 'Roman', 'age' => 44]);
        $index->delete(['name' => 'Ivan', 'age' => 17]);
        $index->delete(['name' => 'Artur', 'age' => 28]);
        $tree = "
_____
";
        $this->assertSame($tree, $index->printTree());
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
