<?php

namespace Btree\Test\Builder;

use Btree\Builder\Builder;
use Btree\Builder\Enum\EnumOperator;
use Btree\Builder\Enum\EnumSort;
use Btree\Builder\Exception\EmptyFieldException;
use Btree\Builder\Exception\InvalidConditionValueException;
use Btree\Builder\Exception\MissedFieldValueException;
use Btree\Index\Btree\Index;
use Btree\Index\Exception\MissedPropertyException;
use Btree\Index\IndexInterface;
use Btree\Test\Index\Btree\Person;
use PHPUnit\Framework\TestCase;

/**
 * Test of BuilderTest
 *
 * @package assassin215k/btree
 */
class BuilderTest extends TestCase
{
    private IndexInterface $index;

    /**
     * @var IndexInterface[]
     */
    private array $indexes;

    /**
     * @var Person[]
     */
    private array $data;

    public function setUp(): void
    {
        $this->data = [
            new Person('Olga', 28, country: 'PL'),
            new Person('Owen', 17, country: 'RU'),
            new Person('Lisa', 44, country: 'UA'),
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
        $this->index = new Index('name');
        $this->indexes = ['name' => $this->index];
    }

    public function testWhereEmptyField()
    {
        $builder = new Builder($this->indexes, $this->data);

        $this->expectException(EmptyFieldException::class);
        $builder->where('', EnumOperator::IsNull);
    }

    public function testWhereLessThen()
    {
        $indexes = [];
        $builder = new Builder($indexes, $this->data);
        $builder->where('name', EnumOperator::Equal, 'Olga');
        $builder->andWhere('age', EnumOperator::LessThen, 50);

        /** @var Person[] $result */
        $result = $builder->run();
        $this->assertSame(2, count($result));

        $builder = new Builder($indexes, $this->data);
        $builder->where('name', EnumOperator::Equal, 'Olga');
        $builder->andWhere('age', EnumOperator::LessThen, 28);

        /** @var Person[] $result */
        $result = $builder->run();
        $this->assertSame(1, count($result));
    }

    public function testWhereBetween()
    {
        $indexes = [];
        $builder = new Builder($indexes, $this->data);
        $builder->where('name', EnumOperator::Equal, 'Olga');
        $builder->andWhere('age', EnumOperator::Between, [20, 10]);

        /** @var Person[] $result */
        $result = $builder->run();
        $this->assertSame(1, count($result));

        $indexes = [];
        $builder = new Builder($indexes, $this->data);
        $builder->where('name', EnumOperator::Equal, 'Olga');
        $builder->andWhere('age', EnumOperator::Between, [20, 28]);

        /** @var Person[] $result */
        $result = $builder->run();
        $this->assertSame(1, count($result));

        $indexes = [];
        $builder = new Builder($indexes, $this->data);
        $builder->where('name', EnumOperator::Equal, 'Olga');
        $builder->andWhere('age', EnumOperator::Between, [18, 28]);

        /** @var Person[] $result */
        $result = $builder->run();
        $this->assertSame(2, count($result));
    }

    public function testWhereLessThenOrEqual()
    {
        $indexes = [];
        $builder = new Builder($indexes, $this->data);
        $builder->andWhere('age', EnumOperator::LessThenOrEqual, 21);

        /** @var Person[] $result */
        $result = $builder->run();
        $this->assertSame(4, count($result));
    }

    public function testWhereGreaterThen()
    {
        $indexes = [];
        $builder = new Builder($indexes, $this->data);
        $builder->andWhere('age', EnumOperator::GreaterThen, 21);

        /** @var Person[] $result */
        $result = $builder->run();
        $this->assertSame(8, count($result));
    }

    public function testWhereGreaterThenOrEqual()
    {
        $indexes = [];
        $builder = new Builder($indexes, $this->data);
        $builder->andWhere('age', EnumOperator::GreaterThenOrEqual, 21);

        /** @var Person[] $result */
        $result = $builder->run();
        $this->assertSame(9, count($result));
    }

    public function testWhereIsNull()
    {
        $indexes = [];
        $builder = new Builder($indexes, $this->data);
        $builder->andWhere('country', EnumOperator::IsNull);

        /** @var Person[] $result */
        $result = $builder->run();
        $this->assertSame(9, count($result));
    }

    public function testWhereValueFail()
    {
        $indexes = [];
        $builder = new Builder($indexes, $this->data);

        $this->expectException(MissedFieldValueException::class);
        $builder->andWhere('country', EnumOperator::Equal);
    }

    public function testWhereValueFailBetween()
    {
        $indexes = [];
        $builder = new Builder($indexes, $this->data);

        $this->expectException(InvalidConditionValueException::class);
        $builder->andWhere('age', EnumOperator::Between, []);
    }

    public function testWhereValueFailBetween2()
    {
        $indexes = [];
        $builder = new Builder($indexes, $this->data);

        $this->expectException(InvalidConditionValueException::class);
        $builder->andWhere('age', EnumOperator::Between, ['', '5']);
    }

    public function testWhereValueFailBetween3()
    {
        $indexes = [];
        $builder = new Builder($indexes, $this->data);

        $this->expectException(InvalidConditionValueException::class);
        $builder->andWhere('age', EnumOperator::Between, [new \DateTime(), '5']);
    }

    public function testWhereValueFailEqual()
    {
        $indexes = [];
        $builder = new Builder($indexes, $this->data);

        $this->expectException(InvalidConditionValueException::class);
        $builder->andWhere('age', EnumOperator::Equal, []);
    }

    public function testWhereValueFailOther()
    {
        $indexes = [];
        $builder = new Builder($indexes, $this->data);

        $this->expectException(InvalidConditionValueException::class);
        $builder->andWhere('age', EnumOperator::LessThenOrEqual, []);
    }

    public function testOrderAsc()
    {
        $indexes = [];
        $builder = new Builder($indexes, $this->data);
        $builder->where('name', EnumOperator::Equal, 'Olga');
        $builder->order('age', EnumSort::ASC);

        /** @var Person[] $result */
        $result = $builder->run();

        $this->assertSame(18, $result[0]->age);
    }

    public function testOrderDesc()
    {
        $indexes = [];
        $builder = new Builder($indexes, $this->data);
        $builder->where('name', EnumOperator::Equal, 'Olga');
        $builder->order('age', EnumSort::DESC);

        /** @var Person[] $result */
        $result = $builder->run();
        $this->assertSame(28, $result[0]->age);
    }

    public function testOrderFail()
    {
        $indexes = [];
        $builder = new Builder($indexes, $this->data);

        $this->expectException(EmptyFieldException::class);
        $builder->order('', EnumSort::DESC);
    }

    public function testOrder()
    {
        $item = new Person('Olga', 18);
        $data = [
            $item,
            new Person('Olga', 28)
        ];
        $indexes = [];
        $builder = new Builder($indexes, $data);
        $builder->order('name', EnumSort::DESC);
        /** @var Person[] $result */
        $result = $builder->run();

        $this->assertSame(18, $result[0]->age);

        $item = new Person('Olga', 18);
        $data = [
            $item,
            new Person('Olga', 28)
        ];
        $indexes = [];
        $builder = new Builder($indexes, $data);
        $builder->order('name', EnumSort::ASC);
        /** @var Person[] $result */
        $result = $builder->run();

        $this->assertSame(18, $result[0]->age);
    }

    public function testCheckField()
    {
        $data = [
            new \DateTime()
        ];
        $indexes = [];
        $builder = new Builder($indexes, $data);
        $builder->where('name', EnumOperator::IsNull);
        $this->expectException(MissedPropertyException::class);
        $builder->run();
    }

    public function testSelectIndex()
    {
        $data = [];
        $indexes = [
            'name-age' => new Index(['name', 'age']),
            'name' => new Index(['name']),
            'age' => new Index('age'),
            'country' => new Index('country'),
        ];
        $builder = new Builder($indexes, $data);
        $builder->where('name', EnumOperator::IsNull);
        $result = $builder->selectIndex();
        $this->assertSame($indexes['name'], $result[0]);
        $this->assertSame(['name'], $result[1]);


        $builder = new Builder($indexes, $data);
        $builder->andWhere('name', EnumOperator::IsNull);
        $builder->andWhere('age', EnumOperator::LessThen, 20);
        $result = $builder->selectIndex();
        $this->assertSame($indexes['name-age'], $result[0]);
        $this->assertSame(['name', 'age'], $result[1]);


        $builder = new Builder($indexes, $data);
        $builder->andWhere('name', EnumOperator::Equal, 'Lisa');
        $builder->andWhere('age', EnumOperator::LessThen, 20);
        $builder->andWhere('country', EnumOperator::IsNull);
        $result = $builder->selectIndex();
        $this->assertSame($indexes['name-age'], $result[0]);
        $this->assertSame(['name', 'age'], $result[1]);


        $builder = new Builder($indexes, $data);
        $builder->andWhere('country', EnumOperator::IsNull);
        $builder->andWhere('name', EnumOperator::Equal, 'Lisa');
        $builder->andWhere('age', EnumOperator::LessThen, 20);
        $result = $builder->selectIndex();
        $this->assertSame($indexes['name-age'], $result[0]);
        $this->assertSame(['name', 'age'], $result[1]);


        $builder = new Builder($indexes, $data);
        $builder->andWhere('name', EnumOperator::Equal, 'Lisa');
        $builder->andWhere('age', EnumOperator::LessThen, 20);
        $builder->where('country', EnumOperator::IsNull);
        $result = $builder->selectIndex();
        $this->assertSame($indexes['country'], $result[0]);
        $this->assertSame(['country'], $result[1]);
    }

    public function testRun()
    {
        foreach ($this->data as $item) {
            $this->indexes['name']->insert($item);
        }
        $builder = new Builder($this->indexes, $this->data);
        $builder->andWhere('name', EnumOperator::Equal, 'Lisa');
        $builder->andWhere('age', EnumOperator::LessThen, 50);
        $builder->andWhere('age', EnumOperator::GreaterThen, 10);
        $builder->andWhere('age', EnumOperator::Between, [45, 15]);
        /** @var Person[] $result */
        $result = $builder->run();
        $this->assertSame(2, count($result));

        $builder = new Builder($this->indexes, $this->data);
        $builder->andWhere('name', EnumOperator::Equal, 'Lisa');
        $builder->andWhere('age', EnumOperator::LessThenOrEqual, 50);
        $builder->andWhere('age', EnumOperator::GreaterThenOrEqual, 10);
        $builder->andWhere('country', EnumOperator::IsNull);
        $result = $builder->run();
        $this->assertSame(1, count($result));
        $this->assertSame(34, $result[0]->age);

        $builder = new Builder($this->indexes, $this->data);
        $builder->andWhere('name', EnumOperator::LessThenOrEqual, 'Roman');
        $builder->andWhere('name', EnumOperator::LessThen, 'Sofia');
        $builder->andWhere('age', EnumOperator::LessThenOrEqual, 50);
        $builder->andWhere('name', EnumOperator::Between, ['A','Z']);
        $result = $builder->run();

        $this->assertSame(12, count($result));

        $builder = new Builder($this->indexes, $this->data);
        $builder->andWhere('name', EnumOperator::LessThenOrEqual, 'Roman');
        $builder->andWhere('name', EnumOperator::LessThen, 'Sofia');
        $builder->andWhere('age', EnumOperator::LessThenOrEqual, 50);
        $result = $builder->run();

        $this->assertSame(12, count($result));

        $builder = new Builder($this->indexes, $this->data);
        $builder->andWhere('name', EnumOperator::LessThenOrEqual, 'Roman');
        $builder->andWhere('name', EnumOperator::LessThenOrEqual, 'Sofia');
        $builder->andWhere('age', EnumOperator::LessThenOrEqual, 50);
        $result = $builder->run();

        $this->assertSame(12, count($result));

        $builder = new Builder($this->indexes, $this->data);
        $builder->andWhere('name', EnumOperator::LessThenOrEqual, 'Roman');
        $builder->andWhere('name', EnumOperator::GreaterThen, 'A');
        $builder->andWhere('age', EnumOperator::LessThenOrEqual, 50);
        $result = $builder->run();

        $this->assertSame(12, count($result));

        $builder = new Builder($this->indexes, $this->data);
        $builder->andWhere('name', EnumOperator::LessThenOrEqual, 'Roman');
        $builder->andWhere('name', EnumOperator::GreaterThenOrEqual, 'A');
        $builder->andWhere('age', EnumOperator::LessThenOrEqual, 50);
        $result = $builder->run();

        $this->assertSame(12, count($result));
    }
}
