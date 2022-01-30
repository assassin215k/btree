<?php

namespace Btree\Index\Btree;

use Btree\Index\Btree\Node\NodeInterface;

/**
 * Interface IndexInterface
 *
 * Index item
 *
 * @package assassin215k/btree
 */
interface IndexInterface
{
    public function __construct(array | string $fields);

    public function insert(object $value): void;

    public function delete(string | object | array $target): bool;

    public function search(string $key): array;

    public function lessThan(string $key): array;

    public function lessThanOrEqual(string $key): array;

    public function graterThan(string $key): array;

    public function graterThanOrEqual(string $key): array;

    public function between(string $form, string $to): array;

    public function printTree(): string;
}
