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
$gender = [null, 0, 1];
$countries = ["UA", "RU", "PL", "GB", "USA", null];
for ($i = 5; $i < 1000; $i++) {
    $data[] = new Person(
        'User',
$i % 60,
//        rand(15, 50),
        $gender[array_rand($gender, 1)],
        $countries[array_rand($countries, 1)]
    );
}

Index::$nodeSize = 3;
$collection = new IndexedCollection($data);
$collection->addIndex(['name', 'age']);
$collection->addIndex('age');
$collection->addIndex('country');
echo "=====", "\n";

$collection->add(new Person('Sofia', 18));
$collection->add(new Person('Sofia', 19));
$collection->add(new Person('Sofia', 20));
$collection->add(new Person('Sofia', 21));
$collection->add(new Person('Sofia', 22));
$collection->add(new Person('Sofia', 23));

//$keys = array (
//    0 => 'N>37',
//    1 => 'K-28',
//    2 => 'N<18',
//    3 => 'K-18',
//    4 => 'N>18',
//);
//
////$keys = array (
////  0 => 'N<37',
////  1 => 'K-37',
////  2 => 'N>37',
////);
//
//$first = Index::getFirstKey($keys, 'K-30', true);
//$last = Index::getLastKey($keys, 'K-20', true);
//
//$flippedKeys = array_flip($keys);
//$keys = array_slice($keys, !is_null($first) ? $flippedKeys[$first] : array_key_last($keys));
//if (!is_null($first)) {
//    $flippedKeys = array_flip($keys);
//    $keys = array_slice($keys, 0, $flippedKeys[$last] + 1);
//}
//
//die;

$result = $collection
    ->createBuilder()
    ->where('age', EnumOperator::Beetwen, [20, 30])
    ->addOrder('age', EnumSort::DESC)
    ->addOrder('name', EnumSort::ASC)
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
echo "=====", "\n";
