<?php

namespace Btree\Index\Btree\Node;

use Btree\Index\Btree\Node\Data\Data;
use Btree\Index\Btree\Node\Data\DataInterface;

/**
 * Class Node
 *
 * @package assassin215k/btree
 */
final class Node implements NodeInterface
{
    public ?Node $prevNode = null;
    public ?Node $nextNode = null;

    /**
     * Current number of keys
     *
     * @var int
     */
    public int $keyTotal = 0;

    /**
     * Current number of child nodes
     *
     * @var int
     */
    public int $nodeTotal = 0;

    /**
     * @var int id of node, only use for debug
     */
    private readonly int $id;

    /**
     * An array of keys
     *
     * @var DataInterface[]|NodeInterface[]
     */
    private array $keys = [];

    /**
     * @param int $degree Minimum degree (defines the range for number of keys)
     * @param bool $isLeaf Is true when node is a leaf
     */
    public function __construct(
        public bool $isLeaf = true,
        array $keys = null,
        int $keyTotal = null,
        int $nodeTotal = null
    ) {
        static $id;

        $this->id = ++$id;

        if ($keys) {
            $this->keys = $keys;
            $this->keyTotal = $keyTotal ?? count($keys);
        }

        if ($nodeTotal) {
            $this->nodeTotal = $nodeTotal;
        }
    }

    public function getPrevNode(): ?NodeInterface
    {
        return $this->prevNode;
    }

    public function setPrevNode(?NodeInterface $node): void
    {
        $this->prevNode = $node;
    }

    public function getNextNode(): ?NodeInterface
    {
        return $this->nextNode;
    }

    public function setNextNode(?NodeInterface $node): void
    {
        $this->nextNode = $node;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $index
     *
     * @return NodeInterface
     */
    public function getNodeByKey(string $index): NodeInterface
    {
        return $this->keys[$index];
    }

    /**
     * Get all Node and Data key array
     *
     * @return array
     */
    public function getKeys(): array
    {
        return $this->keys;
    }

    /**
     * @return bool
     */
    public function isLeaf(): bool
    {
        return $this->isLeaf;
    }

    /**
     * @param bool $isLeaf
     *
     * @return void
     */
    public function setLeaf(bool $isLeaf): void
    {
        $this->isLeaf = $isLeaf;
    }

    /**
     * Return data key count
     *
     * @return int
     */
    public function count(): int
    {
        return $this->keyTotal;
    }

    /**
     * Return node key count
     *
     * @return int
     */
    public function nodeTotal(): int
    {
        return $this->nodeTotal;
    }

    /**
     * @param string $key
     * @param int|null $nodeTotal
     * @param array|null $keys
     *
     * @return string
     */
    public function getChildNodeKey(string $key, int $nodeTotal = null, array $keys = null): string
    {
        if (is_null($nodeTotal)) {
            $nodeTotal = $this->nodeTotal;
        }

        if (is_null($keys)) {
            $keys = $this->keys;
        }

        if ($nodeTotal === 1) {
            return array_key_first($keys);
        }

        if ($nodeTotal === 2) {
            $keyList = array_keys($keys);

            return $keyList[1] < $key ? $keyList[0] : $keyList[2];
        }

        $toMiddle = $nodeTotal % 2 ? $nodeTotal : $nodeTotal - 1;
        $newNodeTotal = intval($nodeTotal / 2);
        if ($nodeTotal % 2) {
            $newNodeTotal++;
        }

        /** @var NodeInterface[] $middle */
        $middle = array_slice($keys, $toMiddle, 1);
        $middleKey = array_key_first($middle);

        if ($middleKey < $key) {
            return $this->getChildNodeKey(
                $key,
                $newNodeTotal,
                array_slice($keys, 0, $toMiddle, true)
            );
        }

        return $this->getChildNodeKey(
            $key,
            $nodeTotal - $newNodeTotal,
            array_slice($keys, $toMiddle + 1, preserve_keys: true)
        );
    }

    /**
     * Replace key between neighbour nodes
     * with last key of prev node
     * or first key of next node
     *
     * @param Node $child
     * @param bool $replacePrev
     *
     * @return void
     */
    public function replaceNextPrevKey(NodeInterface $child, bool $replacePrev): void
    {
        // Get a key to move
        $lastItem = $replacePrev ? $child->prevNode->extractLast() : $child->nextNode->extractFirst();
        $lastKey = array_key_first($lastItem);

        // Get a node to replace
        $target = $this->searchKeyPrev($lastKey, $replacePrev);
        $key = array_key_first($target);
        $child->insertKey($key, $target[$key], 0);
        $this->replaceKey($lastItem, $key, keyOnly: true);
    }

    /**
     * Extract last Node with the node key
     *
     * @return array<string, NodeInterface>
     */
    public function extractLast(): array
    {
        if (!$this->keyTotal) {
            return [];
        }

        $this->keyTotal--;

        return array_splice($this->keys, -1);
    }

    /**
     * Extract first Node with the node key
     *
     * @return array<string, NodeInterface>
     */
    public function extractFirst(): array
    {
        if (!$this->keyTotal) {
            return [];
        }

        $this->keyTotal--;

        return array_splice($this->keys, 0, 1);
    }

    /**
     * Search a key to replace with new middle key
     *
     * @param string $key
     * @param bool $prev
     * @param array|null $keys
     * @param int|null $total
     *
     * @return array<string, DataInterface>
     */
    private function searchKeyPrev(string $key, bool $prev, array $keys = null, int $total = null): array
    {
        if (is_null($keys)) {
            $keys = $this->keys;

//            if (!$prev) {
//                $keys = array_reverse($keys);
//            }
        }

        if (is_null($total)) {
            $total = $this->keyTotal;
        }

        if ($total === 1) {
            if ($prev) {
                return array_slice($keys, 1, 1, true);
            }

            return array_slice($keys, 0, 1, true);
        }

        $full = $total * 2 + 1;
        $offset = ($total % 2) ? intval($full / 2) : ceil($full / 2);

        if ($prev) {
            $slicedKeys = array_slice($keys, 0, $offset - 1, preserve_keys: true);

            if (array_key_last($slicedKeys) > $key) {
                $slicedKeys = array_slice($keys, $offset - 1, preserve_keys: true);
            }

            return $this->searchKeyPrev($key, $prev, $slicedKeys, intval($total / 2));
        }

        $slicedKeys = array_slice($keys, 1, $offset - 1, preserve_keys: true);
        if (array_key_last($slicedKeys) < $key) {
            $slicedKeys = array_slice($keys, $offset - 1, preserve_keys: true);
        }

        return $this->searchKeyPrev($key, $prev, $slicedKeys, intval($total / 2));
    }

    /**
     * Insert new value
     *
     * @param string $key
     * @param object $value
     * @param int|null $position
     */
    public function insertKey(string $key, object $value, int $position = null): void
    {
        if ($this->hasKey($key)) {
            $this->keys[$key]->add($value);

            return;
        }

        $value = new Data($value);

        if (!$position) {
            $position = $this->getKeyPosition($key);
        }

        /**
         * Insert new key at position and return
         */
        $this->keys = array_slice($this->keys, 0, $position, true)
            + [$key => $value]
            + array_slice($this->keys, $position, $this->keyTotal - $position, true);

        $this->keyTotal++;
    }

    public function hasKey(string $key): bool
    {
        return array_key_exists($key, $this->keys);
    }

    /**
     * Search position for new value
     *
     * @param string $key
     *
     * @return int
     */
    private function getKeyPosition(string $key): int
    {
        $i = 0;
        foreach (array_keys($this->keys) as $k) {
            if ($k < $key) {
                return $i;
            }

            $i++;
        }

        return $i;
    }

    /**
     * Replace specified key or all keys with new prepared keys array
     *
     * @param array $array
     * @param string|null $key
     * @param bool $fullReplace
     * @param bool $keyOnly Set true if replace one Data and one Node, sat false if only Data.
     *                      Ignored if $fullReplace is false
     *
     * @return void
     */
    public function replaceKey(array $array, string $key = null, bool $fullReplace = false, bool $keyOnly = false): void
    {
        if ($fullReplace) {
            $this->keys = $array;
            $this->keyTotal = 1;
            $this->nodeTotal = 2;

            return;
        }

        $index = array_flip(array_keys($this->keys))[$key];

        $before = array_slice($this->keys, 0, $index, true);
        $after = array_slice($this->keys, $index + 1, preserve_keys: true);

        $this->keys = $before
            + $array
            + $after;

        if (!$keyOnly) {
            $this->keyTotal++;
            $this->nodeTotal++;
        }
    }

    public function replaceThreeWithOne(string $key, NodeInterface $node, array $keys, bool $next): void
    {
        if ($next) {
            $this->keys = array_slice($this->keys, 0, $keys[$key], preserve_keys: true)
                + [$key => $node]
                + array_slice($this->keys, $keys[$key] + 3, preserve_keys: true);
        } else {
            $this->keys = array_slice($this->keys, 0, $keys[$key] - 2, preserve_keys: true)
                + [$key => $node]
                + array_slice($this->keys, $keys[$key] + 1, preserve_keys: true);
        }

        $this->nodeTotal--;
        $this->keyTotal--;
    }

    public function dropKey(string $key): DataInterface
    {
        if ($this->keys[$key] instanceof DataInterface) {
            $this->keyTotal--;
        } else {
            $this->nodeTotal--;
        }

        $data = $this->keys[$key];

        unset($this->keys[$key]);

        return $data;
    }
}
