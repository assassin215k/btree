<?php

namespace Btree\Node;

/**
 * Interface INode
 *
 * @package assassin215k/btree
 */
interface NodeInterface
{
    public function insertKey(int $key): void;

    public function selectKey(int $key): ?string;

    public function split(int $key, NodeInterface $node): void;

    public function traverse();

    public function searchNode(int $key): ?NodeInterface;
}
