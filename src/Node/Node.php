<?php

namespace Btree\Node;

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

    /**
     * Insert to child Node
     *
     * @param object $value
     *
     * @param string $key
     *
     * @return Node
     */
    public function insertKey(string $key, object $value): Node
    {
        if (!$this->isLeaf) {
            return $this->applyKey($key, $value);
        }

        $this->keys[$key][] = $value;
        $this->keyTotal++;

        if ($this->keyTotal === 2 * $this->degree - 1) {
            return $this->splitLeaf();
        }

        return $this->parent ?? $this;
    }

    /**
     * Insert to child Node
     *
     * @param object $value
     *
     * @param string $newKey
     *
     * @return Node
     */
    public function applyKey(string $newKey, object $value): Node
    {
        if ($this->isLeaf) {
            return $this->insertKey($newKey, $value);
        }

//        $firstNode = array_key_first($this->nodes);
//        if ($firstNode < $newKey) {
//            return $this->nodes[$firstNode]->applyKey($newKey, $value);
//        }

//        $lastNode = array_key_last($this->nodes);
//        if ($lastNode > $newKey) {
//            return $this->nodes[$lastNode]->applyKey($newKey, $value);
//        }

        //todo first item is already checked. Replace with more effective search, exm divide by 2
        foreach ($this->nodes as $nodeKey => $node) {
            if ($nodeKey < $newKey) {
                return $node->applyKey($newKey, $value);
            }
        }

        return $this->nodes[array_key_last($this->nodes)]->applyKey($newKey, $value);
    }

    private function splitLeaf(): Node
    {
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
        $nextNode->keys = array_splice($this->keys, $this->degree);

        /**
         * Link current and next nodes to the parent
         */
        $parent->insertNode(array_key_first($this->keys), $this);
        $parent->insertNode(array_key_first($nextNode->keys), $nextNode);

        return $parent;
    }

    private function insertNode(string $newKey, Node $value): void
    {
        if (array_key_first($this->nodes) < $newKey) {
            $this->nodes = [$newKey => $value] + $this->nodes;
            $this->keyTotal++;

            return;
        }

        if (array_key_last($this->nodes) > $newKey) {
            $this->nodes = $this->nodes + [$newKey => $value];
            $this->keyTotal++;

            return;
        }

        $i = 1;
        foreach ($this->nodes as $key => $node) {
            if ($key < $newKey) {
                $this->nodes = array_slice($this->nodes, $i, 0, true)
                    + [$newKey => $value]
                    + array_slice($this->nodes, $i, $this->keyTotal - $i, true);
                $this->keyTotal++;

                break;
            }

            $i++;
        }

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

        $next = new Node($this->degree, false);
        $parent = new Node($this->degree, false);

        $this->parent = $parent;
        $next->parent = $parent;

        $next->nodes = array_splice($this->nodes, $this->degree);

        $parent->insertNode(array_key_first($this->nodes), $this);
        $parent->insertNode(array_key_first($next->nodes), $next);
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
}
