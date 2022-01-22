<?php

namespace Btree\Index\Btree;

/**
 * Interface IndexInterface
 *
 * Index item
 *
 * @package assassin215k/btree
 */
interface IndexInterface
{
    public function insert(object $value): void;

    public function search(string $key): array;

    public function printTree(): void;
}
