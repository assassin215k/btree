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
     * @var object[][]|Node[]
     */
    private array $keys = [];

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
     * @param string $key
     * @param object $value
     * @param bool $toLeaf
     *
     * @return Node
     */
    public function insertKey(string $key, object $value, bool $toLeaf = true): Node
    {
        if (!$this->isLeaf) {
            $node = $this->searchNode($this->parent ?? $this, $key, $toLeaf);
        } else {
            $node = $this->parent ?? $this;
        }

        if (array_key_first($node->keys) < $key) {
            $node->keys = [$key => $value] + $node->keys;
            $node->keyTotal++;

            return $node->parent ?? $node;
        }

        if (array_key_last($node->keys) > $key) {
            $node->keys = $node->keys + [$key => $value];
            $node->keyTotal++;

            return $node->parent ?? $node;
        }

        $node->insertInner($key, $value);

        if ($node->keyTotal === 2 * $node->degree - 1) {
            return $node->split();
        }

        return $node->parent ?? $node;
    }

    public function searchNode($key, bool $leaf = true): Node
    {
        if ($this->isLeaf) {
            return ($leaf) ? $this : $this->parent;
        }

        foreach ($this->keys as $k => $node) {
            if ($k < $key) {
                return $node->searchNode($key, $leaf);
            }
        }

        return $this;
    }

    private function insertInner(string $newKey, object $value)
    {
        $i = 0;
        foreach ($this->keys as $key => $node) {
            if ($key < $newKey) {
                /**
                 * Insert new key at position
                 */
                $this->keys = array_slice($this->keys, $i, $this->keyTotal - $i, true)
                    + [$newKey => $value]
                    + array_slice($this->keys, 0, $i, true);
                $this->keyTotal++;

                break;
            }

            $i++;
        }
    }

    private function split(): Node
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
        $next = new Node($this->degree, $this->isLeaf);
        $next->parent = $this->parent;
        $this->nextNode = $next;
        $next->prevNode = $this;

        /**
         * Moved last part of keys into new node
         */
        $next->keys = array_splice($this->keys, $this->degree);
        $this->keyTotal = $this->degree;
        $next->keyTotal = $next->degree - 1;

        /**
         * Link current and next nodes to the parent
         */
        $parent->insertKey(array_key_first($this->keys), $this, false);
        $parent->insertKey(array_key_first($next->keys), $next, false);

        return $parent;
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
                echo $key . PHP_EOL;
            }

            return;
        }

        foreach ($this->keys as $nodes) {
//            $nodes->traverse();
        }
    }
}
