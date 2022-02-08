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

        $print = '
_____N<Owen17
__________K-Roman44
__________K-Peter31
__________K-Owen27
_____K-Owen17
_____N>Owen17
__________K-Olga28
__________K-Olga18
_____K-Lisa44
_____N<Artur28
__________K-Lisa34
__________K-Ivan17
_____K-Artur28
_____N>Artur28
__________K-Alex31
__________K-Alex21
';

        $this->assertSame($print, $index->printTree());

        $this->expectException(MissedPropertyException::class);
        $index->insert(new \DateTime());
    }

    public function testDeleteFromLeaf()
    {
        Index::$nodeSize = 3;
        $index = new Index(['name', 'age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }

        $tree = '
_____N<Owen17
__________K-Roman44
__________K-Peter31
__________K-Owen27
_____K-Owen17
_____N>Owen17
__________K-Olga28
__________K-Olga18
_____K-Lisa44
_____N<Artur28
__________K-Lisa34
__________K-Ivan17
_____K-Artur28
_____N>Artur28
__________K-Alex31
__________K-Alex21
';
        $this->assertSame($tree, $index->printTree());

        $this->assertTrue($index->delete(['name' => 'Olga', 'age' => 18]));
        $tree = '
_____N<Owen17
__________K-Roman44
__________K-Peter31
_____K-Owen27
_____N>Owen17
__________K-Owen17
__________K-Olga28
_____K-Lisa44
_____N<Artur28
__________K-Lisa34
__________K-Ivan17
_____K-Artur28
_____N>Artur28
__________K-Alex31
__________K-Alex21
';
        $this->assertSame($tree, $index->printTree());

        $this->assertTrue($index->delete(['name' => 'Olga', 'age' => 28]));
        $tree = '
_____N<Owen17
__________K-Roman44
__________K-Peter31
_____K-Owen27
_____N>Owen17
__________K-Owen17
__________K-Lisa44
__________K-Lisa34
__________K-Ivan17
_____K-Artur28
_____N>Artur28
__________K-Alex31
__________K-Alex21
';
        $this->assertSame($tree, $index->printTree());

        $this->assertFalse($index->delete(['name' => 'Olga', 'age' => 28]));
        $this->assertFalse($index->delete(['name' => 'Olga', 'age' => 28]));
        $this->assertFalse($index->delete(['name' => 'Olga', 'age' => 28]));
        $this->assertSame($tree, $index->printTree());


        $this->assertTrue($index->delete(['name' => 'Lisa', 'age' => 34]));
        $this->assertTrue($index->delete(['name' => 'Owen', 'age' => 27]));
        $this->assertTrue($index->delete(['name' => 'Peter', 'age' => 31]));
        $tree = '
_____N<Owen17
__________K-Roman44
__________K-Owen17
_____K-Lisa44
_____N>Owen17
__________K-Ivan17
__________K-Artur28
__________K-Alex31
__________K-Alex21
';
        $this->assertSame($tree, $index->printTree());

        $this->assertTrue($index->delete(['name' => 'Roman', 'age' => 44]));
        $this->assertTrue($index->delete(['name' => 'Ivan', 'age' => 17]));
        $this->assertTrue($index->delete(['name' => 'Artur', 'age' => 28]));
        $tree = '
_____K-Owen17
_____K-Lisa44
_____K-Alex31
_____K-Alex21
';
        $this->assertSame($tree, $index->printTree());

        $this->assertFalse($index->delete(['name' => 'Artur', 'post' => true]));
        $this->assertFalse($index->delete(['name' => 'Artur']));
    }

    public function testDeleteFromNotLeaf()
    {
        Index::$nodeSize = 3;
        $index = new Index(['name', 'age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }

        $this->assertTrue($index->delete(['name' => 'Artur', 'age' => 28]));
        $tree = '
_____N<Owen17
__________K-Roman44
__________K-Peter31
__________K-Owen27
_____K-Owen17
_____N>Owen17
__________K-Olga28
__________K-Olga18
_____K-Lisa44
_____N>Artur28
__________K-Lisa34
__________K-Ivan17
__________K-Alex31
__________K-Alex21
';
        $this->assertSame($tree, $index->printTree());

        $this->assertTrue($index->delete(['name' => 'Lisa', 'age' => 44]));
        $tree = '
_____N<Owen17
__________K-Roman44
__________K-Peter31
__________K-Owen27
_____K-Owen17
_____N>Owen17
__________K-Olga28
__________K-Olga18
_____K-Lisa34
_____N>Artur28
__________K-Ivan17
__________K-Alex31
__________K-Alex21
';
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

        Index::$nodeSize = 20;
        $index = new Index(['age']);
        $data = $this->data;
        for ($i = 5; $i < 5000; $i++) {
            $data[] = new Person(
                'User',
                $i % 60,
            );
        }
        foreach ($data as $person) {
            $index->insert($person);
        }

        $result = $index->between('K-20', 'K-40');
        $this->assertSame(1750, count($result));
    }

    public function testSearch()
    {
        Index::$nodeSize = 3;
        $index = new Index(['age']);
        foreach ($this->data as $person) {
            $index->insert($person);
        }

        $result = $index->search('K-17');
        $this->assertSame(2, count($result));

        $result = $index->search('K-23');
        $this->assertSame(0, count($result));

        $result = $index->search('K-21');
        $this->assertSame(1, count($result));
    }
}
