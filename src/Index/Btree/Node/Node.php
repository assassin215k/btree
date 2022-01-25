<?php

namespace Btree\Index\Btree\Node;

/**
 * Class Node
 *
 * @package assassin215k/btree
 */
final class Node implements NodeInterface
{
    public ?Node $prevNode = null;
    public ?Node $nextNode = null;
    public ?Node $parent = null;

    private int $id = 1;

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
        static $id;
        if (!$id) {
            $id = 0;
        }
        $this->id = ++$id;
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
        if ($this->isLeaf) {
            return $this->hasKey($key) ? $this->keys[$key] : [];
        }

        return $this->searchNode($key)->selectKey($key);
    }

    /**
     * Check if key exist in the current node
     *
     * @param string $key
     *
     * @return bool
     */
    private function hasKey(string $key): bool
    {
        return key_exists($key, $this->keys);
    }

    /**
     * @param $key
     * @param bool $leaf
     *
     * @return $this
     */
    public function searchNode($key, bool $leaf = true): Node
    {
        if ($this->isLeaf) {
            return $leaf ? $this : $this->parent;
        }

        if (array_key_exists($key, $this->keys)) {
            return $this->keys[$key]->searchNode($key, $leaf);
        }

        foreach ($this->keys as $k => $node) {
            if ($k < $key) {
                return $node->searchNode($key, $leaf);
            }
        }

        return $this->searchNode($key, $leaf);
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
     * Drop key from current leaf
     *
     * @param string $key
     */
    public function dropKey(string $key): void
    {
        if (!$this->isLeaf) {
            $node = $this->searchLeaf($key);
            $node->dropKey($key);

            return;
        }

        unset($this->keys[$key]);
        $this->keyTotal--;

        $oldKey = $this->key;
        $this->key = array_key_first($this->keys);

        if (!$this->parent) {
            return;
        }

        $this->parent->updateKey($oldKey, $this->key);

        $this->merge();
//        $this->parent->merge($this);
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

        if (array_key_exists($key, $this->keys)) {
            return $this->keys[$key]->searchLeaf($key);
        }

        $targetKey = array_key_first($this->keys);
        foreach (array_reverse($this->keys, true) as $k => $node) {
            if ($k > $key) {
                $targetKey = $k;
                break;
            }
        }

        return $this->keys[$targetKey]->searchLeaf($key);
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

                $this->parent?->updateKey($oldNodeKey, $this->key);

                return;
            }

            $i++;
        }

        $this->keys += $newValue;
        $this->key = array_key_first($this->keys);

        $this->parent?->updateKey($oldNodeKey, $this->key);
    }

    /**
     * Move keys of the node to another node if the keys don't enough
     *
     * @param Node $node
     *
     * @return void
     */
//    private function merge(Node $node): void
    private function merge(): void
    {
        if ($this->degree - 1 < $this->keyTotal) {
            return;
        }

        if ($this->prevNode) {
            $this->prevNode->nextNode = $this->nextNode;
        }

        if ($this->nextNode) {
            $this->nextNode->prevNode = $this->prevNode;
        }

        unset($this->parent->keys[$this->key]);
        $this->parent->keyTotal--;

        /**
         * Move keys to previous node
         */
        if ($this->prevNode && $this->prevNode->keyTotal < $this->degree) {
            $this->prevNode->keys = $this->prevNode->keys + $this->keys;
            $this->prevNode->keyTotal += $this->keyTotal;

//            $this->prevNode->checkParent();

            return;
        }

        /**
         * Move keys to next node
         */
        if ($this->nextNode && $this->nextNode->keyTotal < $this->degree) {
            $this->nextNode->keys = $this->keys + $this->nextNode->keys;
            $this->nextNode->keyTotal += $this->keyTotal;

            $oldKey = $this->nextNode->key;
            $this->nextNode->key = $this->key;

//            $this->nextNode->checkParent();

            $this->nextNode?->parent->updateKey($oldKey, $this->nextNode->key);

            return;
        }

        if ($this->prevNode) {
            $nodeToUpdate = $this->prevNode;
            $nodeToUpdate->keys = $nodeToUpdate->keys + $this->keys;
            $nodeToUpdate->keyTotal += $this->keyTotal;

//            $nodeToUpdate->checkParent();

            $nodeToUpdate->split();

            return;
        }

        if ($this->nextNode) {
            $nodeToUpdate = $this->nextNode;
            $nodeToUpdate->keys = $this->keys + $nodeToUpdate->keys;
            $nodeToUpdate->keyTotal += $this->keyTotal;
            $oldKey = $nodeToUpdate->key;
            $nodeToUpdate->key = $this->key;

//            $nodeToUpdate->checkParent();

            $nodeToUpdate?->parent->updateKey($oldKey, $nodeToUpdate->key);

            $nodeToUpdate->split();
        }
    }

    private function checkParent(): void
    {
        if ($this->parent && !$this->parent->keyTotal) {
            $this->parent = $this->parent->parent;
        }
    }

    /**
     * Split full node into two and link to the parent
     *
     * @return void
     */
    private function split(): void
    {
        if ($this->keyTotal < 2 * $this->degree - 1) {
            return;
        }

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
         * Moved last part of keys into new node
         */
        $next->keys = array_splice($this->keys, $this->degree);
        $next->keyTotal = $this->keyTotal - $this->degree;
        $this->keyTotal = $this->degree;

        if (!$next->isLeaf) {
            foreach ($next->keys as $nextChild) {
                $nextChild->parent = $next;
            }
        }


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
         * Linked next and prev leafs
         */
        if (!$this->isLeaf) {
            return;
        }

        $oldNext = $this->nextNode;
        if ($oldNext) {
            $next->nextNode = $oldNext;
            $oldNext->prevNode = $next;
        }

        $this->nextNode = $next;
        $next->prevNode = $this;
    }

    /**
     * Insert to key of Node or linked object and return root if possible
     *
     * @param string $key
     * @param object $value
     */
    public function insertKey(string $key, object $value): void
    {
        $oldNodeKey = $this->key ?? false;
        $this->insert($key, $value);

        if ($oldNodeKey && $oldNodeKey !== $this->key) {
            $this->parent?->updateKey($oldNodeKey, $this->key);
        }

        $this->split();
    }

    /**
     * Search position for new value and insert
     *
     * @param string $newKey
     * @param object $value
     */
    public function insert(string $newKey, object $value): void
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
        $oldKey = $this->key ?? false;
        $this->keyTotal++;
        $this->key = array_key_first($this->keys);

        if ($oldKey && $oldKey !== $this->key) {
            $this->parent?->updateKey($oldKey, $this->key);
        }
    }

    /**
     * Search root of tree recursively to check if root is changed
     *
     * @return NodeInterface
     */
    public function getRoot(): NodeInterface
    {
        if ($this->parent) {
            return $this->parent->getRoot();
        }

        if ($this->keyTotal === 1 && !$this->isLeaf) {
            return $this->keys[$this->key];
        }

        return $this;
    }
}
