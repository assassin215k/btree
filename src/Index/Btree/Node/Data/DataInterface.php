<?php

namespace Btree\Index\Btree\Node\Data;

/**
 * Interface DataInterface
 *
 * @package assassin215k/btree
 */
interface DataInterface
{
    public function add(object $value): void;

    public function get(): array;

    public function total(): int;
}
