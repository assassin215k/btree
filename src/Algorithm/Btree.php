<?php

namespace Btree\Algorithm;

use Btree\IndexedCollectionInterface;
use Btree\Node\NodeInterface;

/**
 * Class Btree
 *
 * algorithm realization
 *
 * @package assassin215k/btree
 */
class Btree implements AlgorithmInterface
{
    private NodeInterface $rootNode;

    public function createIndex(string|array $fieldName): self
    {
        return $this;
    }
}
