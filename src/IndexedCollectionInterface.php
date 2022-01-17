<?php

namespace Btree;

use Btree\Algorithm\IndexAlgorithm;
use Btree\SortOrder\IndexSortOrder;

/**
 * Interface IndexedCollectionInterface
 *
 * Collection methods
 *
 * @package assassin215k/btree
 */
interface IndexedCollectionInterface
{
    public function addIndex(string|array $fieldName, IndexAlgorithm $algorithm): void;
    public function sortBy(string $field, IndexSortOrder $order): self;
    public function search(array $where): array;
    public function getData(): array;
}
