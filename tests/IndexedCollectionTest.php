<?php

namespace Btree\Test;

use Btree\Builder\Builder;
use Btree\Builder\BuilderInterface;
use Btree\Exception\IndexDuplicationException;
use Btree\Exception\IndexMissingException;
use Btree\Exception\InvalidIndexClassException;
use Btree\Index\Btree\Index;
use Btree\IndexedCollection;
use Btree\Test\Index\Btree\Person;
use PHPUnit\Framework\TestCase;

/**
 * Test of IndexedCollection
 *
 * @package assassin215k/btree
 */
class IndexedCollectionTest extends TestCase
{

    public function testConstruct()
    {
        $collection = new IndexedCollection([], options: [
            'builderClass' => \DateTime::class,
            'indexClass' => \DateTime::class,
        ]);

        $this->assertSame(\DateTime::class, $collection::$defaultBuilderClass);
        $this->assertSame(\DateTime::class, $collection::$defaultIndexClass);


        IndexedCollection::$defaultIndexClass = Index::class;
        IndexedCollection::$defaultBuilderClass = Builder::class;
    }

    public function testDropIndexFail()
    {
        $data = [new Person('Olga', 28)];
        $collection = new IndexedCollection($data);
        $this->expectException(IndexMissingException::class);
        $collection->dropIndex('name');
    }

    public function testAddIndex()
    {
        $data = [new Person('Olga', 28)];
        $collection = new IndexedCollection($data);
        $collection->addIndex('name');
        $collection->add(new Person('Olga', 18));
        $collection->delete('Olga');
        $collection->dropIndex('name');

        $this->assertTrue(true);
    }

    public function testAddIndexFail()
    {
        $data = [new Person('Olga', 28)];
        $collection = new IndexedCollection($data);
        $collection->addIndex('name');
        $this->expectException(IndexDuplicationException::class);
        $collection->addIndex('name');
    }

    public function testAddIndexFail2()
    {
        IndexedCollection::$defaultIndexClass = \ArrayObject::class;
        $data = [new Person('Olga', 28)];
        $collection = new IndexedCollection($data);
        $this->expectException(InvalidIndexClassException::class);
        $collection->addIndex(['name']);
        IndexedCollection::$defaultIndexClass = Index::class;
    }

    public function testPrintFirstIndex()
    {
        $index = \Mockery::mock('Btree\Index\IndexInterface');
        $index->shouldReceive('insert');
        $index->shouldReceive('printTree')->andReturn('test');
        $data = [new Person('Olga', 28)];
        $collection = new IndexedCollection($data);
        $this->assertNull($collection->printFirstIndex());

        $collection->addIndex(['name'], $index);
        $this->assertIsString($collection->printFirstIndex());
    }

    public function testCreateBuilder()
    {
        $data = [new Person('Olga', 28)];
        $collection = new IndexedCollection($data);
        $builder = $collection->createBuilder();
        $this->assertInstanceOf(BuilderInterface::class, $builder);
    }
}
