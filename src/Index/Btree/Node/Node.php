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
    public function __construct(public bool $isLeaf = true, array $keys = null, int $keyTotal = null)
    {
        static $id;

        $this->id = ++$id;

        if ($keys) {
            $this->keys = $keys;
            $this->keyTotal = $keyTotal ?? count($keys);
        }
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
     * Replace specified key or all keys with new prepared keys array
     *
     * @param array $array
     * @param string|null $key
     * @param bool $fullReplace
     *
     * @return void
     */
    public function replaceKey(array $array, string $key = null, bool $fullReplace = false): void
    {
        if ($fullReplace) {
            $this->keys = $array;
            $this->keyTotal = 1;
            $this->nodeTotal = 2;

            return;
        }

        $index = array_flip(array_keys($this->keys))[$key];

        $before = array_slice($this->keys, 0, $index, true);
        $after = array_slice($this->keys, $index + 1, $this->keyTotal - $index + 1, true);

        $this->keys = $before
            + $array
            + $after;
        $this->keyTotal++;
        $this->nodeTotal++;
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
     * @param int $position
     *
     * @return array
     */
    public function splitKeys(int $position): array
    {
        $this->keyTotal = $this->keyTotal - $position + 1;

        return array_splice($this->keys, $position);
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
     * @param $key
     * @param bool $leaf
     *
     * @return $this
     */
    public function searchNode($key): NodeInterface
    {
        if ($this->isLeaf) {
            return $this;
        }

        return $this->searchNode($key);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getChildNodeKey(string $key): string
    {
        return $this->search($key, $this->nodeTotal, $this->keys);
    }

    /**
     * @param string $key
     * @param int $nodeTotal
     * @param array $keys
     *
     * @return string
     */
    private function search(string $key, int $nodeTotal, array $keys): string
    {
        if ($nodeTotal === 1) {
            return array_key_first($keys);
        }

        $toMiddle = $nodeTotal % 2 ? $nodeTotal : $nodeTotal + 1;
        /** @var NodeInterface[] $middle */
        $middle = array_slice($keys, $toMiddle, 1);

        if ($nodeTotal === 2) {
            $keyList = array_keys($keys);

            return $keyList[1] < $key ? $keyList[0] : $keyList[2];
        }

        $nodeTotal = intdiv($nodeTotal, 2);

        $middleKey = array_key_first($middle);

        if ($middleKey < $key) {
            return $this->search($key, $toMiddle - $nodeTotal, array_slice($keys, 0, $toMiddle, true));
        }

        return $this->search($key, $nodeTotal, array_slice($keys, $toMiddle + $nodeTotal, preserve_keys: true));
    }

    /**
     * traverse and print all nodes in a subtree rooted with this node
     *
     * @return void
     */
    public function traverse(): void
    {
        if ($this->isLeaf) {
            foreach ($this->keys as $key => $values) {
                echo "$key: " . count($values) . PHP_EOL;
            }

            return;
        }

        foreach ($this->keys as $node) {
            $node->traverse();
        }
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
        if (array_key_exists($key, $this->keys)) {
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

    public function hasKey(string $key): bool
    {
        return array_key_exists($key, $this->keys);
    }
}
