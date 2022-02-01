<?php

require_once 'vendor/autoload.php';

use Btree\Builder\Enum\EnumOperator;
use Btree\Builder\Enum\EnumSort;
use Btree\Index\Btree\Index;
use Btree\IndexedCollection;
use Btree\Test\Index\Btree\Person;

$data = [
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

//$data = [];
for ($i = 5;$i < 1000;$i++) {
    $data[] = new Person('User', $i);
}

Index::$nodeSize = 3;
$collection = new IndexedCollection($data);
$collection->addIndex(['name', 'age']);
echo "=====","\n";

$collection->add(new Person('Sofia', 18));
$collection->add(new Person('Sofia', 19));
$collection->add(new Person('Sofia', 20));
$collection->add(new Person('Sofia', 21));
$collection->add(new Person('Sofia', 22));
$collection->add(new Person('Sofia', 23));

$result = $collection
    ->createBuilder()
    ->where('age', EnumOperator::LessThen, 20)
    ->addOrder('name', EnumSort::ASC)
    ->addOrder('age', EnumSort::ASC)
    ->run();
die;


$item = $collection->searchFirstIndex('K-Olga18');
$collection->delete(['name' => 'Alex', 'age' => 21]);
$collection->delete(['name' => 'Owen', 'age' => 17]);
//$collection->printFirstIndex();
$collection->delete(['name' => 'Olga', 'age' => 18]);
//$collection->printFirstIndex();
$collection->delete(['name' => 'Olga', 'age' => 28]);
//$collection->printFirstIndex();
$collection->delete(['name' => 'Olga', 'age' => 28]);
$collection->delete(['name' => 'Lisa', 'age' => 34]);
$collection->delete(['name' => 'Owen', 'age' => 27]);
$collection->delete(['name' => 'Peter', 'age' => 31]);
$collection->delete(['name' => 'Roman', 'age' => 44]);
$collection->delete(['name' => 'Ivan', 'age' => 17]);
$collection->delete(['name' => 'Artur', 'age' => 28]);
$collection->printFirstIndex();

//$collection->printFirstIndex();
echo "=====","\n";
