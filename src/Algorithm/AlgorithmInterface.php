<?php

namespace Btree\Algorithm;

use Btree\IndexedCollectionInterface;

/**
 * Interface AlgorithmInterface
 *
 * algorithm methods
 *
 * @package assassin215k/btree
 */
interface AlgorithmInterface
{
    public function createIndex(string|array $fieldName): self;
}
