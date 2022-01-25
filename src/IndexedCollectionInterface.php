<?php

namespace Btree;

use Btree\Enum\IndexEnum;
use Btree\Builder\BuilderInterface;
use Btree\Index\Btree\IndexInterface;

/**
 * Interface IndexedCollectionInterface
 *
 * Collection methods
 *
 * @package assassin215k/btree
 */
interface IndexedCollectionInterface
{
    public function addIndex(string | array $fieldName, IndexInterface $index): void;

    public function dropIndex(string | array $fieldName): void;

    public function add(object $item): void;

    public function delete(string $key): void;

    public function printFirstIndex(): void;

    public function createBuilder(): BuilderInterface;
}
