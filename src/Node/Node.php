<?php

namespace Btree\Node;

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

    public bool $isLeaf = true;
    public int $keyTotal = 0;

    /**
     * @var NodeInterface[]|null
     */
    private array $children = [];
    private array $keys = [];

    /**
     * @param int $degree
     */
    public function __construct(private readonly int $degree)
    {
    }

    /**
     * Insert to child Node
     *
     * @param int $key
     *
     * @return void
     */
    public function insertKey(int $key): void
    {
    }

    /**
     * Select from keys in leaf
     *
     * @param int $key
     *
     * @return string|null
     */
    public function selectKey(int $key): ?string
    {
        if ($this->isLeaf) {
            return null;
        }

        return (key_exists($key, $this->keys)) ? $this->keys[$key] : null;
    }

    /**
     * Split child Node
     *
     * @param int $key the index of child
     * @param NodeInterface $node
     *
     * @return void
     */
    public function split(int $key, NodeInterface $node): void
    {
    }

    public function echo()
    {
        $i = 0;

        for (; $i <= $this->keyTotal; $i++) {
            if (!$this->isLeaf) {
                $this->children[$i]->traverse();
            }

            echo $this->keys[$i] . PHP_EOL;
        }
    }

    public function searchNode(int $key): ?NodeInterface
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
}
