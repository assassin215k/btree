<?php

namespace Btree\Algorithm;

use Btree\Node\Node;
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
    public function createIndex(string|array $fieldName): self
    {
        return $this;
    }

    public function addItem(mixed &$cache, array $fields, object $item): void
    {
    }

    /**
     * @return NodeInterface
     */
    public function getCache(): NodeInterface
    {
        return new Node();
    }
}
