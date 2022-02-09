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
    public function __construct(
        public string $name,
        public int $age,
        public ?int $gender = null,
        public ?string $country = null
    ) {
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
