<?php

namespace Btree\Index;

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

    public function search(string $value): ?string;

    public function printTree(): void;
}
