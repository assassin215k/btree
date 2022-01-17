<?php

require_once 'vendor/autoload.php';

use Btree\Algorithm\IndexAlgorithm;
use Btree\IndexedCollection;

interface IPerson
{
    public function getName(): string;

    public function getAge(): int;
}

class Person implements IPerson
{
    public function __toString(): string
    {
        return $this->name;
    }

    public function __construct(private string $name, private int $age)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAge(): int
    {
        return $this->age;
    }
}

$data = [
    new Person('Olga', 28),
    new Person('Owen', 17),
    new Person('Lisa', 44),
    new Person('Alex', 31),
];

$collection = new IndexedCollection($data);
$collection->addIndex('name', IndexAlgorithm::BTREE);

//var_dump($collection);
echo "ok","\n";
