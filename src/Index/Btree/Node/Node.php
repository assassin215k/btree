<?php

namespace Btree\Index\Btree\Node;

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

    public string $key;

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
     * Insert to key of Node or linked object
     *
     * @param string $key
     * @param object $value
     *
     * @return NodeInterface|null
     */
    public function insertKey(string $key, object $value): ?NodeInterface
    {
        $this->insert($key, $value);

        $this->parent?->updateKey($this->key);

        if ($this->keyTotal === 2 * $this->degree - 1) {
            $this->split();

            return $this->getRoot();
        }

        return null;
    }

    /**
     * @param string $newKey
     * @param object $value
     *
     * @return string
     */
    private function insert(string $newKey, object $value): void
    {
        if ($this->isLeaf) {
            if (array_key_exists($newKey, $this->keys)) {
                $this->keys[$newKey][] = $value;

                return;
            }

            $value = [$value];
        }

        $i = 0;
        foreach ($this->keys as $key => $item) {
            if ($key < $newKey) {
                /**
                 * Insert new key at position
                 */
                $this->keys = array_slice($this->keys, 0, $i, true)
                    + [$newKey => $value]
                    + array_slice($this->keys, $i, $this->keyTotal - $i, true);

                $this->postInsert();

                return;
            }

            $i++;
        }

        $this->keys = $this->keys + [$newKey => $value];
        $this->postInsert();
    }

    private function postInsert(): void
    {
        $this->keyTotal++;
        $this->key = array_key_first($this->keys);
    }

    private function updateKey(string $newKey): void
    {
        if (array_key_exists($newKey, $this->keys)) {
            return;
        }

        $i = 0;
        foreach ($this->keys as $key => $node) {
            if ($key < $newKey) {
                $this->keys = array_slice($this->keys, 0, $i, true)
                    + [$newKey => $node]
                    + array_slice($this->keys, $i + 1, $this->keyTotal - $i - 1, true);

                return;
            }

            $i++;
        }
    }

    private function split(): void
    {
        /**
         * If $this has a parent split $this and link keys of news nodes to the parent
         */
        $parent = $this->parent;
        if (!$parent) {
            /**
             * Created parent for this node and new next node
             */
            $parent = new Node($this->degree, false);
            $this->parent = $parent;
        }

        /**
         * Created a new next node
         */
        $next = new Node($this->degree, $this->isLeaf);
        $next->parent = $parent;

        /**
         * Move next of $this to new next node
         */
        if ($this->nextNode) {
            $next->nextNode = $this->nextNode;
        }
        $this->nextNode = $next;
        $next->prevNode = $this;

        /**
         * Moved last part of keys into new node
         */
        $next->keys = array_splice($this->keys, $this->degree);
        $this->keyTotal = $this->degree;
        $next->keyTotal = $next->degree - 1;

        $next->key = array_key_first($next->keys);

        /**
         * Link current node to the parent if is new root
         */
        if (!array_key_exists($this->key, $parent->keys)) {
            $parent->insertKey($this->key, $this);
        }

        /**
         * Link new node to the parent
         */
        $parent->insertKey($next->key, $next);

        if ($parent->keyTotal === 2 * $parent->degree - 1) {
            $parent->split();
        }
    }

    private function getRoot(): NodeInterface
    {
        return $this->parent ? $this->parent->getRoot() : $this;
    }

    public function searchLeaf(string $key): Node
    {
        if ($this->isLeaf) {
            return $this;
        }

        foreach ($this->keys as $k => $node) {
            if ($k > $key) {
                return $node->searchLeaf($key);
            }
        }

        return $this->keys[array_key_first($this->keys)];
    }

    public function searchNode($key, bool $leaf = true): Node
    {
        if ($this->isLeaf) {
            return ($leaf) ? $this : $this->parent;
        }

        foreach ($this->keys as $k => $node) {
            if ($k > $key) {
                return $node->searchNode($key, $leaf);
            }
        }

        return $this;
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
