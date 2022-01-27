<?php

require_once 'vendor/autoload.php';

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
//for ($i = 5;$i < 1000;$i++) {
//    $data[] = new Person('User', $i);
//}

Index::$nodeSize = 3;
$collection = new IndexedCollection($data);
//$collection->addIndex(['name', 'age']);
echo "=====","\n";

//$collection->add(new Person('Sofia', 18));

//$collection->delete(['name' => 'Olga', 'age' => 18]);
//$collection->delete(['name' => 'Olga', 'age' => 28]);
//$collection->delete(['name' => 'Olga', 'age' => 28]);
//$collection->delete(['name' => 'Olga', 'age' => 28]);
//$collection->delete(['name' => 'Olga', 'age' => 28]);
//$collection->delete(['name' => 'Olga', 'age' => 28]);
//$collection->delete(['name' => 'Olga', 'age' => 28]);

$collection->printFirstIndex();
echo "=====","\n";
