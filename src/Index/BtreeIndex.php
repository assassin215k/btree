<?php

namespace Btree\Index;

use Btree\Exception\MissedFieldException;
use Btree\Exception\MissedPropertyException;
use Btree\Node\Node;
use Btree\Node\NodeInterface;

/**
 * Class Index
 *
 * Index to contain index cache
 *
 * @package assassin215k/btree
 */
class BtreeIndex implements IndexInterface
{
    public static int $nodeSize;

    private ?Node $root = null;

    /**
     * @var string[] list of fields
     */
    private readonly array $fields;

    /**
     * @throws MissedFieldException
     *
     * @param array|string $fields
     * @param int $nodeSize
     */
    public function __construct(array | string $fields, int $nodeSize = 3)
    {
        self::$nodeSize = $nodeSize;

        $this->fields = is_array($fields) ? $fields : [$fields];

        if (!count($this->fields)) {
            throw new MissedFieldException();
        }

        foreach ($this->fields as $field) {
            if (empty($field)) {
                throw new MissedFieldException();
            }
        }
    }

    /**
     * @throws MissedPropertyException
     *
     * @param object $value
     *
     * @return void
     */
    public function insert(object $value): void
    {
        $key = $this->getKey($value);

        // If tree is empty create new and insert a key
        if (is_null($this->root)) {
            $this->root = new Node(self::$nodeSize);
            $this->root->insertKey($key);

            return;
        }

        // If root is full, then tree grows in height
        if ($this->root->keyTotal > 2 * self::$nodeSize - 1) {
            // Create a node for new root
            $newNode = new Node(self::$nodeSize);

            // Make old root as child of new root
            $newNode->children[0] = $this->root;

            // Split the old root and move 1 key to the new root
            $newNode->splitChild(0, $this->root);

            /**
             * New root has two children now.
             * Decide which of the two children is going to have new key
             */
            $i = 0;
            if ($newNode->selectKey(0) < $key) {
                $i++;
            }

            $newNode->children[$i]->insertNonFull($key);

            // Change root
            $this->root = $newNode;

            return;
        }

        // If root is not full, call insertNonFull for root
        $this->root->insertNonFull($key);
    }

    /**
     * @throws MissedPropertyException
     *
     * @param object $value
     *
     * @return string
     */
    private function getKey(object $value): string
    {
        $key = '';
        foreach ($this->fields as $field) {
            if (empty($value->$field)) {
                throw new MissedPropertyException($field, $value);
            }

            $key .= $value->$field;
        }

        return $key;
    }

    public function search(string $value): ?string
    {
        $node = $this->searchNode($value);
        if (is_null($node)) {
            return null;
        }

        return $node->selectKey($value);
    }

    /**
     * @param string $key
     *
     * @return NodeInterface|null
     */
    private function searchNode(string $key): ?NodeInterface
    {
        if (is_null($this->root)) {
            return null;
        }

        return $this->root->searchNode($key);
    }

    public function printTree(): void
    {
        if (!is_null($this->root)) {
            $this->root->traverse();
        }
    }
}
