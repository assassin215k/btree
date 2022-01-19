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
];

$collection = new IndexedCollection($data);
$collection->addIndex('name');

//$collection->addSortBy('name', IndexSortOrder::DESC);
//$collection->add(new Person('Alex', 31));
//var_dump($collection);
echo "ok","\n";
