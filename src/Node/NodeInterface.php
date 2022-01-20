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

    public function insertKey(string $key, object $value): void;

    public function selectKey(string $key): array;

    public function traverse(): void;

    public function searchNode(string $key): ?NodeInterface;

    public function isLeaf(): bool;

    public function getDegree(): int;
}
