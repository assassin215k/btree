<?php

namespace Btree\Node;

/**
 * Interface INode
 *
 * @package assassin215k/btree
 */
interface NodeInterface
{
    public function insertKey(string $key, object $value, bool $toLeaf): Node;

    public function selectKey(string $key): array;

    public function traverse(): void;

    public function searchNode($key, bool $leaf): ?NodeInterface;
}
