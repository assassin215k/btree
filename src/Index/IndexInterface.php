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
    public function add(object $value): void;

    public function search(string $value): array;
}
