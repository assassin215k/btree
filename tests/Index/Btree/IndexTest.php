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

    public function testDeleteFromLeaf()
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

    public function testGetFirstKey()
    {
        $keys = [
            0 => 'N<44',
            1 => 'K-44',
            2 => 'N>44',
            3 => 'K-34',
            4 => 'N>34',
            5 => 'K-24',
            6 => 'N>24',
            7 => 'K-14',
            8 => 'N>14',
        ];

        $this->assertSame('N<44', Index::getFirstKey($keys, 'K-50'));
        $this->assertSame('N>44', Index::getFirstKey($keys, 'K-40'));
        $this->assertSame('N<44', Index::getFirstKey($keys, 'K-44'));
        $this->assertSame('K-44', Index::getFirstKey($keys, 'K-44', true));
        $this->assertSame('N>34', Index::getFirstKey($keys, 'K-25'));
        $this->assertSame('N>34', Index::getFirstKey($keys, 'K-25', true));
        $this->assertSame('N>24', Index::getFirstKey($keys, 'K-14'));
        $this->assertSame('K-14', Index::getFirstKey($keys, 'K-14', true));
        $this->assertSame('N>14', Index::getFirstKey($keys, 'K-2'));
        $this->assertSame('N>14', Index::getFirstKey($keys));

        $keys = [
            0 => 'K-44',
            1 => 'K-34',
            2 => 'K-31',
            3 => 'K-28',
            4 => 'K-22',
        ];

        $this->assertSame(null, Index::getFirstKey($keys, key: 'K-50', include: true, isLeaf: true));
        $this->assertSame('K-44', Index::getFirstKey($keys, key: 'K-44', include: true, isLeaf: true));
        $this->assertSame('K-44', Index::getFirstKey($keys, key: 'K-40', include: true, isLeaf: true));
        $this->assertSame('K-31', Index::getFirstKey($keys, key: 'K-31', include: true, isLeaf: true));
        $this->assertSame('K-34', Index::getFirstKey($keys, key: 'K-31', include: false, isLeaf: true));
        $this->assertSame('K-22', Index::getFirstKey($keys, key: 'K-2', include: false, isLeaf: true));
        $this->assertSame('K-22', Index::getFirstKey($keys, isLeaf: true));
    }

    public function testGetLastKey()
    {
        $keys = [
            0 => 'N<44',
            1 => 'K-44',
            2 => 'N>44',
            3 => 'K-34',
            4 => 'N>34',
            5 => 'K-24',
            6 => 'N>24',
            7 => 'K-14',
            8 => 'N>14',
        ];

        $this->assertSame('N<44', Index::getLastKey($keys, 'K-50'));
        $this->assertSame('N>44', Index::getLastKey($keys, 'K-40'));
        $this->assertSame('N>44', Index::getLastKey($keys, 'K-44'));
        $this->assertSame('K-44', Index::getLastKey($keys, 'K-44', true));
        $this->assertSame('N>34', Index::getLastKey($keys, 'K-25'));
        $this->assertSame('N>34', Index::getLastKey($keys, 'K-25', true));
        $this->assertSame('N>14', Index::getLastKey($keys, 'K-14'));
        $this->assertSame('K-14', Index::getLastKey($keys, 'K-14', true));
        $this->assertSame('N>14', Index::getLastKey($keys, 'K-2'));
        $this->assertSame('N<44', Index::getLastKey($keys));

        $keys = [
            0 => 'K-44',
            1 => 'K-34',
            2 => 'K-31',
            3 => 'K-28',
            4 => 'K-22',
        ];
        $this->assertSame('K-44', Index::getLastKey($keys, key: 'K-50', include: true, isLeaf: true));
        $this->assertSame('K-44', Index::getLastKey($keys, key: 'K-44', include: true, isLeaf: true));
        $this->assertSame('K-34', Index::getLastKey($keys, key: 'K-40', include: true, isLeaf: true));
        $this->assertSame('K-31', Index::getLastKey($keys, key: 'K-31', include: true, isLeaf: true));
        $this->assertSame('K-28', Index::getLastKey($keys, key: 'K-31', include: false, isLeaf: true));
        $this->assertSame(null, Index::getLastKey($keys, key: 'K-2', include: false, isLeaf: true));
        $this->assertSame('K-44', Index::getLastKey($keys, isLeaf: true));

        $keys = [
            0 => 'K-44',
            1 => 'K-34',
            2 => 'K-31',
            3 => 'K-28',
            4 => 'K-27',
            5 => 'K-23',
            6 => 'K-22',
            7 => 'K-21',
            8 => 'K-20',
            9 => 'K-19',
            10 => 'K-18',
            11 => 'K-17',
        ];
        $this->assertSame('K-20', Index::getLastKey($keys, key: 'K-20', include: true, isLeaf: true));

        $keys = [
            0 => 'K-44',
            1 => 'K-34',
            2 => 'K-31',
        ];
        $this->assertSame(null, Index::getLastKey($keys, key: 'K-31', include: false, isLeaf: true));
    }

    public function testGraterThan()
    {
        Index::$nodeSize = 3;
        $index = new Index(['age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }

        $result = $index->greaterThan('K-31');
        $this->assertSame(3, count($result));

        Index::$nodeSize = 10;
        $index = new Index(['age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }
        $result = $index->greaterThan('K-21');
        $this->assertSame(8, count($result));
    }

    public function testLessThan()
    {
        Index::$nodeSize = 3;
        $index = new Index(['age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }

        $result = $index->lessThan('K-30');
        $this->assertSame(7, count($result));

        Index::$nodeSize = 10;
        $index = new Index(['age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }

        $result = $index->lessThan('K-28');
        $this->assertSame(5, count($result));

        Index::$nodeSize = 10;
        $index = new Index(['age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }

        $result = $index->lessThan('K-2');
        $this->assertSame(0, count($result));
    }

    public function testLessThanOrEqual()
    {
        Index::$nodeSize = 3;
        $index = new Index(['age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }

        $result = $index->lessThanOrEqual('K-31');
        $this->assertSame(9, count($result));

        Index::$nodeSize = 10;
        $index = new Index(['age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }

        $result = $index->lessThanOrEqual('K-21');
        $this->assertSame(4, count($result));
    }

    public function testGreaterThanOrEqual()
    {
        Index::$nodeSize = 3;
        $index = new Index(['age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }

        $result = $index->greaterThanOrEqual('K-31');
        $this->assertSame(5, count($result));

        Index::$nodeSize = 10;
        $index = new Index(['age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }

        $result = $index->greaterThanOrEqual('K-21');
        $this->assertSame(9, count($result));

        Index::$nodeSize = 10;
        $index = new Index(['age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }

        $result = $index->greaterThanOrEqual('K-100');
        $this->assertSame(0, count($result));
    }

    public function testBetween()
    {
        Index::$nodeSize = 3;
        $index = new Index(['age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }

        $result = $index->between('K-10', 'K-30');
        $this->assertSame(7, count($result));

        Index::$nodeSize = 10;
        $index = new Index(['age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }

        $result = $index->between('K-30', 'K-10');
        $this->assertSame(7, count($result));

        Index::$nodeSize = 10;
        $index = new Index(['age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }

        $result = $index->between('K-50', 'K-60');
        $this->assertSame(0, count($result));
    }
}
