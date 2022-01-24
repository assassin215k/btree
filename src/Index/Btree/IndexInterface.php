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

    public function delete(string $key): void;

    public function search(string $key): array;

    public function lessThan(string $key): array;

    public function lessThanOrEqual(string $key): array;

    public function graterThan(string $key): array;

    public function graterThanOrEqual(string $key): array;

    public function between(string $form, string $to): array;

    public function printTree(): void;
}
