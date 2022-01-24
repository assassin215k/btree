<?php

require_once 'vendor/autoload.php';

use Btree\IndexedCollection;

class Person
{
    public function __toString(): string
    {
        return $this->name;
    }

    public function __construct(public string $name, public int $age)
    {
    }
}

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

$collection = new IndexedCollection($data, 3);
$collection->addIndex(['name']);
//$collection->printFirstIndex();
echo "=====","\n";

//$collection->addSortBy('name', IndexSortOrder::DESC);
$collection->add(new Person('Sofia', 18));
//$collection->printFirstIndex();

$collection->delete('Owen');
//$collection->delete('Alex');
$collection->delete('Sofia');
$collection->delete('Roman');
$collection->delete('Peter');
$collection->delete('Olga');
$collection->delete('Lisa');
$collection->delete('Ivan');

//var_dump($collection->findKey('Lisa'));
//var_dump($collection->findKey('Ololo'));

//var_dump($collection);
echo "=====","\n";
