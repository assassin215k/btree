<?php

namespace Btree\Node;

use SplFixedArray;

/**
 * Class Node
 *
 * @package assassin215k/btree
 */
class Node implements NodeInterface
{
    public ?NodeInterface $prevNode = null;
    public ?NodeInterface $nextNode = null;
    public ?NodeInterface $parent = null;

    /**
     * Current number of keys
     *
     * @var int
     */
    public int $keyTotal = 0;

    /**
     * An array of child nodes
     *
     * @var NodeInterface[]
     */
    public array $children = [];

    /**
     * An array of keys
     *
     * @var SplFixedArray
     */
    private array $keys = [];

    /**
     * @param int $degree Minimum degree (defines the range for number of keys)
     * @param bool $isLeaf Is true when node is a leaf
     */
    public function __construct(private readonly int $degree, private readonly bool $isLeaf = true)
    {
//        $this->keys = new SplFixedArray(2 * $this->degree);
//        $this->children = new SplFixedArray(2 * $this->degree - 1);
    }

    /**
     * Insert to child Node
     *
     * @param int $key
     *
     * @return void
     */
    public function insertKey(string $key): void
    {
        $this->keys[] = $key;
        $this->keyTotal++;
    }

    /**
     * Select from keys in leaf
     *
     * @param int $key
     *
     * @return string|null
     */
    public function selectKey(string $key): ?string
    {
        if ($this->isLeaf) {
            return null;
        }

        return (key_exists($key, $this->keys)) ? $this->keys[$key] : null;
    }

    /**
     * traverse and print all nodes in a subtree rooted with this node
     *
     * @return void
     */
    public function traverse(): void
    {
        /**
         * n = keyTotal
         * There are n and n+1 children,
         * traverse through n keys
         * and first n children
         */
        $i = 0;
        for (; $i < $this->keyTotal; $i++) {
            /**
             * If this is not a leaf, then before printing key,
             * traverse the subtree rooted with child.
             */
            if (!$this->isLeaf) {
                $this->children[$i]->traverse();
            }

            echo $this->keys[$i] . PHP_EOL;
        }

        if (!$this->isLeaf) {
            // Print the subtree rooted with last child
            $this->children[$i]->traverse();
        }
    }

    public function searchNode(string $key): ?NodeInterface
    {
        // Find the first index greater than or equal to the aim key
        $index = 0;
        while ($index < $this->keyTotal && $key > $this->keys[$index]) {
            $index++;
        }

        // If the found result is equal to key, return this node
        if ($this->keys[$index] === $key) {
            return $this;
        }

        // If the key is not found here and this is a leaf node
        if ($this->isLeaf) {
            return null;
        }

        // Go to the appropriate child
        return $this->children[$index]->searchNode($key);
    }

    /**
     * A utility function to insert a new key in this node
     * The assumption is, the node must be non-full when this function is called
     *
     * @param string $key
     *
     * @return void
     */
    public function insertNonFull(string $key): void
    {
        // Initialize index as index of rightmost element
        $i = $this->keyTotal - 1;

        // If this is a leaf node
        if ($this->isLeaf) {
            /**
             * The following loop does two things:
             * a) Finds the location of new key to be inserted
             * b) Moves all greater keys to one place ahead
             */
            while ($i >= 0 && $this->keys[$i] > $key) {
                $this->keys[$i + 1] = $this->keys[$i];
                $i--;
            }

            // Insert the new key at found location
            $this->keys[$i + 1] = $key;
            $this->keyTotal++;

            return;
        }

        /**
         * If this node is not leaf
         * Find the child which is going to have the new key
         */
        while ($i >= 0 && $this->keys[$i] > $key) {
            $i--;
        }

        // See if the found child is full
        if ($this->children[$i + 1]->getTotal() === (2 * $this->degree - 1)) {
            // If the child is full, then split it
            $this->splitChild($i + 1, $this->children[$i + 1]);

            /**
             * After split, the middle key of children[$i] goes up and
             * children[$i] is splitted into two. See which of the two
             * is going to have the new key
             */
            if ($this->keys[$i + 1] < $key) {
                $i++;
            }
        }

        $this->children[$i + 1]->insertNonFull($key);
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->keyTotal;
    }

    /**
     * Split child Node
     *
     * @param int $key the index of child
     * @param Node $firstNode
     *
     * @return void
     */
    public function splitChild(string $key, Node $firstNode): void
    {
        // Create second node which is going to store ($degree-1) keys of $firstNode
        $secondNode = new Node($firstNode->getDegree(), $firstNode->isLeaf());
        $secondNode->keyTotal = $this->degree - 1;

        // Copy the last ($degree-1) keys of $firstNode to $secondNode
        for ($i = 0; $i < $this->degree; $i++) {
            $secondNode->keys[$i] = $firstNode->keys[$i + $this->degree];
        }

        if (!$firstNode->isLeaf()) {
            // Copy the last $degree children of $firstNode to $secondNode
            for ($i = 0; $i < $this->degree; $i++) {
                $secondNode->children[$i] = $firstNode->children[$i + $this->degree];
            }
        }

        // Reduce the number of keys in $firstNode
        $firstNode->keyTotal = $this->degree - 1;

        /**
         * Since this node is going to have a new child,
         * create space of new child
         */
        for ($i = $this->keyTotal; $i >= $key + 1; $i--) {
            $this->children[$i + 1] = $this->children[$i];
        }

        // Link the new child to this node
        $this->children[$key + 1] = $secondNode;

        /**
         * A key of $firstNode will move to this node.
         * Find the location of new key and move all greater keys one space ahead
         */
        for ($i = $this->keyTotal - 1; $i >= $key; $i--) {
            $this->keys[$i + 1] = $this->keys[$i];
        }

        // Copy the middle key of y to this node
        $this->keys[$key] = $firstNode->keys[$this->degree - 1];

        // Increment count of keys in this node
        $this->keyTotal++;
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
}
