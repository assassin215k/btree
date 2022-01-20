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
     * @param object $value
     *
     * @param string $key
     *
     * @return Node
     */
    public function insertKey(string $key, object $value): Node
    {
//        if (!$this->keyTotal) {
//            $this->insertKey($key, $value);
//            $this->keyTotal++;
//        }


//        if (!$this->isLeaf && $this->keyTotal) {
//            $node = $this->searchNode($this->parent ?? $this, $key);
//            $node->insertKey($key, $value);
////            $node->insertKey($key, $value);
////            echo 'tesst';
////            die;
//        }

        if (array_key_first($this->keys) < $key) {
            $this->keys = [$key => $value] + $this->keys;
            $this->keyTotal++;

            return $this->parent ?? $this;
        }

        if (array_key_last($this->keys) > $key) {
            $this->keys = $this->keys + [$key => $value];
            $this->keyTotal++;

            return $this->parent ?? $this;
        }

        $this->insertInner($key, $value);

        if ($this->keyTotal === 2 * $this->degree - 1) {
            return $this->split();
        }

        return $this->parent ?? $this;
    }

    public function searchNode(Node $node, string $key): NodeInterface
    {
        if ($node->isLeaf) {
            return $this;
        }

        if (array_key_first($node->keys) < $key) {
            $n = $node->keys[array_key_first($node->keys)];
            return $this->searchNode($n, $key);
        }

        foreach ($node->keys as $k => $n) {
            if ($k < $key) {
                $this->searchNode($n, $key);
            }
        }

        return $node->keys[array_key_last($this->keys)];
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
        $parent->insertKey(array_key_first($this->keys), $this);
        $parent->insertKey(array_key_first($next->keys), $next);

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
