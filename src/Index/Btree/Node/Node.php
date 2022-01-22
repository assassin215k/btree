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

    /**
     * @var string key to link into parent
     */
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
     * Insert to key of Node or linked object and return root if possible
     *
     * @param string $key
     * @param object $value
     *
     * @return NodeInterface|null
     */
    public function insertKey(string $key, object $value): ?NodeInterface
    {
        $oldNodeKey = $this->key ?? false;
        $this->insert($key, $value);

        if ($oldNodeKey && $oldNodeKey !== $this->key) {
            $this->parent?->updateKey($oldNodeKey, $this->key);
        }

        /**
         * Check if current node/leaf is full
         */
        if ($this->keyTotal === 2 * $this->degree - 1) {
            $this->split();

            return $this->getRoot();
        }

        return null;
    }

    /**
     * Search position for new value and insert
     *
     * @param string $newKey
     * @param object $value
     */
    private function insert(string $newKey, object $value): void
    {
        if ($this->isLeaf) {
            /**
             * If the list already has a key than add new value to this key
             */
            if (array_key_exists($newKey, $this->keys)) {
                $this->keys[$newKey][] = $value;

                return;
            }

            /**
             * Else prepare value to insert
             */
            $value = [$value];
        }

        /**
         * Insert new key at position and return
         */
        $i = 0;
        foreach ($this->keys as $key => $item) {
            if ($key < $newKey) {
                $this->keys = array_slice($this->keys, 0, $i, true)
                    + [$newKey => $value]
                    + array_slice($this->keys, $i, $this->keyTotal - $i, true);

                $this->postInsert();

                return;
            }

            $i++;
        }

        /**
         * If position not found insert to the end
         */
        $this->keys = $this->keys + [$newKey => $value];

        $this->postInsert();
    }

    /**
     * Update total and self key of top key of child/list
     *
     * @return void
     */
    private function postInsert(): void
    {
        $this->keyTotal++;
        $this->key = array_key_first($this->keys);
    }

    /**
     * Replace old key with new to same child node
     *
     * @param string $oldNodeKey
     * @param string $newKey
     *
     * @return void
     */
    private function updateKey(string $oldNodeKey, string $newKey): void
    {
        if (array_key_exists($newKey, $this->keys)) {
            return;
        }

        $newValue = [$newKey => $this->keys[$oldNodeKey]];
        unset($this->keys[$oldNodeKey]);

        $i = 0;
        foreach ($this->keys as $key => $node) {
            if ($key < $newKey) {
                $this->keys = array_slice($this->keys, 0, $i, true)
                    + $newValue
                    + array_slice($this->keys, $i, $this->keyTotal - $i, true);
                $this->key = array_key_first($this->keys);

                return;
            }

            $i++;
        }

        $this->keys += $newValue;
        $this->key = array_key_first($this->keys);
    }

    /**
     * Split full node into two and link to the parent
     *
     * @return void
     */
    private function split(): void
    {
        /**
         * Use current parent or create a new one
         */
        $parent = $this->parent;
        if (!$parent) {
            $parent = new Node($this->degree, false);
            $this->parent = $parent;
        }

        /**
         * Creat a new next node
         */
        $next = new Node($this->degree, $this->isLeaf);
        $next->parent = $parent;

        /**
         * Linked next and prev leafs
         */
        if ($this->isLeaf) {
            /**
             * Move next of $this to new next node
             */
            if ($this->nextNode) {
                $next->nextNode = $this->nextNode;
            }
            $this->nextNode = $next;
            $next->prevNode = $this;
        }

        /**
         * Moved last part of keys into new node
         */
        $next->keys = array_splice($this->keys, $this->degree);
        $this->keyTotal = $this->degree;
        $next->keyTotal = $next->degree - 1;

        /**
         * Added top key of new node to self
         */
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

        /**
         * Check if parent is full
         */
        if ($parent->keyTotal === 2 * $parent->degree - 1) {
            $parent->split();
        }
    }

    /**
     * Search root of tree recursively to check if root is changed
     *
     * @return NodeInterface
     */
    private function getRoot(): NodeInterface
    {
        return $this->parent ? $this->parent->getRoot() : $this;
    }

    /**
     * Search leaf of tree
     *
     * @param string $key
     *
     * @return $this
     */
    public function searchLeaf(string $key): Node
    {
        if ($this->isLeaf) {
            return $this;
        }

        foreach (array_reverse($this->keys, true) as $k => $node) {
            if ($k > $key) {
                return $node->searchLeaf($key);
            }
        }

        return $this->keys[array_key_last($this->keys)];
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
