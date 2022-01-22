<?php

namespace Btree\Index\Btree\Node;

/**
 * Interface INode
 *
 * @package assassin215k/btree
 */
interface NodeInterface
{
    public function insertKey(string $key, object $value): ?NodeInterface;

    public function selectKey(string $key): array;

    public function traverse(): void;

    public function searchLeaf(string $key): NodeInterface;

    public function searchNode($key, bool $leaf): NodeInterface;
}
