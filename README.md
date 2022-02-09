# Btree

[![Latest Version](https://img.shields.io/github/release/assassin215k/btree.svg)](https://github.com/assassin215k/btree/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![codecov](https://codecov.io/gh/iceorb-com-ua/btree/branch/master/graph/badge.svg?token=WJLPALOI00&)](https://codecov.io/gh/iceorb-com-ua/btree/branch/master)
[![Codecov](https://img.shields.io/codecov/c/github/iceorb-com-ua/btree/branch/dev?label=codecov%20dev&color=lightgray)](https://codecov.io/gh/iceorb-com-ua/btree/branch/dev)
[![Total Downloads](https://img.shields.io/packagist/dt/assassin215k/btree.svg)](https://packagist.org/packages/assassin215k/btree)

Provides btree-indexation for an object collection. Provide sorting, ordering and composite indexes.
Writes with PSR12 support

## Install
Via Composer

``` bash
$ composer require assassin215k/btree:0.1
```

## Create collection and index
Use objects with same public properties.

``` php
use Btree\IndexedCollection;

$collection = new IndexedCollection($data);
$collection->addIndex(['name', 'age']);
```

Can be used multiple indexes for different properties
``` php
$collection = new IndexedCollection(data: $data);
$collection->addIndex(['name', 'age']);
$collection->addIndex(['name']);
$collection->addIndex('age');
```

Use own Builder and/or Index, that implements BuilderInterface and/or IndexInterface
``` php
use Btree\Builder\BuilderInterface;
use Btree\Index\IndexInterface;

class OwnBuilder implements BuilderInterface{};
class OwnIndex implements IndexInterface{};

$collection = new IndexedCollection(options: [
'builderClass' => OwnBuilder:class
'indexClass' => OwnIndex:class
]);
```

Configure degree of default btree index, 100 by default
``` php
use Btree\Index\Btree\Index;

Index::$nodeSize = 10;
$collection = new IndexedCollection();
```

Use custom index class that implements IndexInterface
``` php
class OwnIndex implements IndexInterface {}

$collection = new IndexedCollection(data: []);
$collection->addIndex('name', new OwnIndex());
```

## Drop index
``` php
$collection->dropIndex(['name', 'age']);
$collection->dropIndex('name');
```

## Add to collection
Add items into collection after creating the one
``` php
$collection = new IndexedCollection();
$collection->addIndex(['name', 'age']);
$collection->add(new SomeClass('Sofia', 18));
```

## Remove from collection
Add items into collection after creating the one
``` php
$collection->delete(['name' => 'Sofia", 'age' => 18]);

$person = new SomeClass('Sofia', 18);
$collection = new IndexedCollection();
$collection->add($person);
..
$collection->delete($person);
```

## Builder
Each builder use for one query
``` php
use Btree\Builder\Enum\EnumOperator;
use Btree\Builder\Enum\EnumSort;

$builder = $collection->createBuilder();

$builder->andWhere('name', EnumOperator::Equal, 'Lisa');

$builder->andWhere('country', EnumOperator::IsNull);

$builder->andWhere('age', EnumOperator::LessThen, 50);
$builder->andWhere('age', EnumOperator::LessThenOrEqual, 50);
$builder->andWhere('age', EnumOperator::GreaterThen, 10);
$builder->andWhere('name', EnumOperator::GreaterThenOrEqual, 'A');

$builder->andWhere('age', EnumOperator::Between, [45, 15]);
$builder->andWhere('name', EnumOperator::Between, ['A','Z']);
$builder->order('age', EnumSort::DESC);
$builder->addOrder('name', EnumSort::ASC);

$builder->run();
// Will return an array of added objects
```
If a collection has multiple indexes, builder use only the one of them that is better to search

Use ```andWhere``` to add new comparison or ```where``` to use from scratch.
``` php
$builder = $collection->createBuilder();

$builder->andWhere('name', EnumOperator::IsNull);

$builder->where('country', 'name', EnumOperator::Between, ['A','Z']);
// will search all in A..Z
```
Similar with order ```addOrder``` to add next order or ```order``` to replace all previous with a new one.



## Testing

``` bash
$ phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email info@iceorb.com.ua instead of using the issue tracker.

## Credits

- [Ihor Fedan](https://github.com/assassin215k)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
