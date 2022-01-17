<?php

namespace Btree\Algorithm;

use Btree\Node\NodeInterface;

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

    public function addItem(mixed &$cache, array $fields, object $item): void;

    public function getCache(): NodeInterface;
}
