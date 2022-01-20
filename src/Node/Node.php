<?php

namespace Btree\Node;

use Btree\Exception\WrongNodeTypeException;
use SplFixedArray;

/**
 * Class Node
 *
 * @package assassin215k/btree
 */
class Node implements NodeInterface
{
    public ?Node $prevNode = null;
    public ?Node $nextNode = null;
    public ?Node $parent = null;

    /**
     * Current number of keys
     *
     * @var int
     */
    public int $keyTotal = 0;

    /**
     * An array of keys
     *
     * @var object[][]
     */
    private array $keys = [];

    /**
     * An array of child nodes
     *
     * @var Node[]
     */
    private array $nodes = [];

    /**
     * @param int $degree Minimum degree (defines the range for number of keys)
     * @param bool $isLeaf Is true when node is a leaf
     */
    public function __construct(private readonly int $degree, private readonly bool $isLeaf = true)
    {
    }

    public function isRoot(): bool
    {
        return !is_null($this->parent);
    }

    /**
     * Insert to child Node
     *
     * @param object $value
     *
     * @param string $key
     *
     * @return void
     */
    public function insertKey(string $key, object $value): void
    {
        if (!$this->isLeaf) {
            $this->applyKey($key, $value);

            return;
        }

        $this->keys[$key][] = $value;
        $this->keyTotal++;

        if ($this->keyTotal === 2 * $this->degree - 1) {
            $this->splitLeaf();
        }
    }

    /**
     * Insert to child Node
     *
     * @param object $value
     *
     * @param string $newKey
     *
     * @return void
     */
    public function applyKey(string $newKey, object $value): void
    {
        if ($this->isLeaf) {
            $this->insertKey($newKey, $value);

            return;
        }

        $firstNode = array_key_first($this->nodes);
        if ($firstNode < $newKey) {
            $this->nodes[$firstNode]->applyKey($newKey, $value);
        }

        $lastNode = array_key_last($this->nodes);
        if ($lastNode > $newKey) {
            $this->nodes[$lastNode]->applyKey($newKey, $value);
        }

        foreach ($this->nodes as $nodeKey => $node) {
            if ($nodeKey < $newKey) {
                $node->applyKey($newKey, $value);

                return;
            }
        }
    }

    private function splitLeaf(): void
    {
        /**
         * split array into 2
         */
        $this->keys = array_chunk($this->keys, $this->degree);

        /**
         * Created parent for this node and new next node
         */
        $parent = new Node($this->degree, false);
        $parent->parent = $this->parent;
        $this->parent = $parent;

        /**
         * Created a new next node
         */
        $nextNode = new Node($this->degree);
        $nextNode->parent = $this->parent;
        $this->nextNode = $nextNode;
        $nextNode->prevNode = $this;

        /**
         * Moved last part of keys into new node
         */
        $nextNode->keys = $this->keys[1];

        /**
         * Moved first part of keys back to current node
         */
        $this->keys = $this->keys[0];

        /**
         * Link current and next nodes to the parent
         */
        $parent->insertNode(array_key_first($this->keys), $this);
        $parent->insertNode(array_key_first($nextNode->keys), $nextNode);
    }

    private function insertNode(string $newKey, Node $value): void
    {
        if (array_key_first($this->nodes) < $newKey) {
            array_unshift($this->nodes, [$newKey => $value]);
        }

        if (array_key_last($this->nodes) > $newKey) {
            $this->nodes[] = [$newKey => $value];
        }

        $i = 1;
        foreach ($this->nodes as $key => $node) {
            if ($key < $newKey) {
                array_splice($this->nodes, $i, 0, [$newKey => $value]);

                break;
            }

            $i++;
        }

        $this->keyTotal++;

        if ($this->keyTotal === 2 * $this->degree - 1) {
            $this->splitNode();
        }
    }

    private function splitNode(): void
    {
        /**
         * split array into 2
         */
        $this->nodes = array_chunk($this->nodes, $this->degree);

        //todo make not leaf node split
    }

    public function searchNode(string $key): NodeInterface
    {
        if ($this->isLeaf) {
            return $this;
        }

        if (array_key_first($this->nodes) < $key) {
            return $this->nodes[array_key_first($this->nodes)]->searchNode($key);
        }

        foreach ($this->nodes as $k => $node) {
            if ($k < $key) {
                $node->searchNode($key);
            }
        }

        return $this->nodes[array_key_last($this->nodes)]->searchNode($key);
    }

    /**
     * Select from keys in leaf
     *
     * @param string $key
     *
     * @return array
     */
    public function selectKey(string $key): array
    {
        return (key_exists($key, $this->keys)) ? $this->keys[$key] : [];
    }

    /**
     * traverse and print all nodes in a subtree rooted with this node
     *
     * @return void
     */
    public function traverse(): void
    {
        if ($this->isLeaf) {
            foreach ($this->keys as $key => $value) {
//                var_dump($key);
                echo $key . PHP_EOL;
            }

            return;
        }

        foreach ($this->nodes as $nodes) {
            $nodes->traverse();
        }
    }

    /**
     * @return int
     */
    public function getDegree(): int
    {
        return $this->degree;
    }

    /**
     * @return bool
     */
    public function isLeaf(): bool
    {
        return $this->isLeaf;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->keyTotal;
    }
}
