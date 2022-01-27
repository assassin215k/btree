<?php

namespace Btree\Index\Btree\Node;

use Btree\Index\Btree\Node\Data\DataInterface;

/**
 * Interface NodeInterface
 *
 * @package assassin215k/btree
 */
interface NodeInterface
{
    public function getId(): int;

    public function isLeaf(): bool;

    public function setLeaf(bool $isLeaf): void;

    public function getKeys(): array;

    public function getNodeByKey(string $index): NodeInterface;

    public function replaceKey(array $array, string $key = null, bool $fullReplace = false): void;

    public function hasKey(string $key): bool;

    public function count(): int;

    public function extractLast(): array;

    public function insertKey(string $key, object $value, int $position = null): void;

    public function getChildNodeKey(string $key): string;
}
