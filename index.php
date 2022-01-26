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

//for ($i = 5;$i < 1000;$i++) {
//    $data[] = new Person('User', $i);
//}

\Btree\Index\Btree\Index::$nodeSize = 3;
$collection = new IndexedCollection($data);
$collection->addIndex(['name', 'age']);
echo "=====","\n";

$collection->add(new Person('Sofia', 18));
echo "=====","\n";
