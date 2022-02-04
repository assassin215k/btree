<?php

namespace Btree\Test\Builder;

use Btree\Builder\Builder;
use Btree\Builder\BuilderInterface;
use Btree\Builder\Enum\EnumOperator;
use Btree\Builder\Enum\EnumSort;
use Btree\Builder\Exception\EmptyFieldException;
use Btree\Builder\Exception\InvalidConditionValueException;
use Btree\Builder\Exception\MissedFieldValueException;
use Btree\Index\Btree\Index;
use Btree\Index\Btree\IndexInterface;
use Btree\Test\Index\Btree\Person;
use Mockery;
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
        $this->indexes = [$this->index];
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

//    public function testRun()
//    {
//    }
}
