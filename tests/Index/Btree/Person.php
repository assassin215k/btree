<?php

namespace Btree\Test\Index\Btree;

/**
 * Class Person
 *
 * Only to run tests
 *
 * @package assassin215k/btree
 */
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
