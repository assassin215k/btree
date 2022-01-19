<?php

namespace Btree\Node;

/**
 * Interface INode
 *
 * @package assassin215k/btree
 */
interface NodeInterface
{
    public function getTotal(): int;

    public function insertKey(string $key): void;

    public function selectKey(string $key): ?string;

    public function splitChild(string $key, Node $firstNode): void;

    public function traverse(): void;

    public function searchNode(string $key): ?NodeInterface;

    public function isLeaf(): bool;

    public function getDegree(): int;

    public function insertNonFull(string $key): void;
}
