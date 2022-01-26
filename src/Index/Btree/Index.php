<?php

namespace Btree\Index\Btree;

use Btree\Exception\MissedFieldException;
use Btree\Exception\MissedPropertyException;
use Btree\Index\Btree\Node\Data\DataInterface;
use Btree\Index\Btree\Node\Node;
use Btree\Index\Btree\Node\NodeInterface;

/**
 * Class Index
 *
 * Index to contain index cache
 *
 * All nodes key start from N< or N>
 * All data keys start from K-
 *
 * @package assassin215k/btree
 */
class Index implements IndexInterface
{
    public static int $nodeSize = 100;
    private readonly int $degree;

    private ?Node $root = null;

    /**
     * @var string[] list of fields
     */
    private readonly array $fields;

    /**
     * @throws MissedFieldException
     *
     * @param array|string $fields
     */
    public function __construct(array | string $fields)
    {
        $this->fields = is_array($fields) ? $fields : [$fields];

        if (!count($this->fields)) {
            throw new MissedFieldException();
        }

        foreach ($this->fields as $field) {
            if (empty($field)) {
                throw new MissedFieldException();
            }
        }

        $this->degree = self::$nodeSize;

        $this->root = new Node();
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

        $this->insertToNode($this->root, $key, $value);

        if ($this->root->count() === $this->degree * 2 - 1) {
            $arrayToReplace = $this->splitRoot($this->root);
            $this->root->replaceKey($arrayToReplace, fullReplace: true);
            $this->root->setLeaf(false);
        }
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
        $key = 'K-';
        foreach ($this->fields as $field) {
            if (empty($value->$field)) {
                throw new MissedPropertyException($field, $value);
            }

            $key .= is_null($value->$field) ? '_' : $value->$field;
        }

        return $key;
    }

    /**
     * Insert to non root Node
     *
     * @param NodeInterface $node
     * @param string $key
     * @param object $value
     *
     * @return void
     */
    private function insertToNode(NodeInterface $node, string $key, object $value): void
    {
        if ($node->hasKey($key)) {
            $node->insertKey($key, $value);

            return;
        }

        if ($node->isLeaf()) {
            $node->insertKey($key, $value);

            return;
        }

        $position = $node->getChildNodeKey($key);
        $child = $node->getNodeByKey($position);

        $this->insertToNode($child, $key, $value);

        if ($child->count() === $this->degree * 2 - 1) {
            $arrayToReplace = $this->splitRoot($child);
            $node->replaceKey($arrayToReplace, $position);
        }
    }

    /**
     * Split full Node
     *
     * @param Node $node
     *
     * @return array
     */
    private function splitRoot(Node $node): array
    {
        $nextNode = new Node($node->isLeaf(), $node->splitKeys($this->degree), $this->degree - 1);

        /** @var DataInterface $value */
        $medianValue = $node->extractLast();
        $key = array_key_first($medianValue);

        $lKey = substr($key, 2);

        /**
         * All nodes key start from N< or N>
         * All Keys start from K-
         */
        return [
            "N<$lKey" => new Node($node->isLeaf(), $node->getKeys(), $node->keyTotal),
            $key => $medianValue[$key],
            "N>$lKey" => $nextNode
        ];
    }

    /**
     * todo unrealized method
     *
     * @param string $key
     *
     * @return void
     */
    public function delete(string $key): void
    {
    }

    /**
     * todo unrealized method
     * @param string $key
     *
     * @return array
     */
    public function search(string $key): array
    {
        return [];
    }

    /**
     * todo unrealized method
     *
     * @return void
     */
    public function printTree(): void
    {
    }

    /**
     * todo unrealized method
     *
     * @param string $key
     *
     * @return array
     */
    public function lessThan(string $key): array
    {
        return [];
    }

    /**
     * todo unrealized method
     *
     * @param string $key
     *
     * @return array
     */
    public function lessThanOrEqual(string $key): array
    {
        return [];
    }

    /**
     * todo unrealized method
     *
     * @param string $key
     *
     * @return array
     */
    public function graterThan(string $key): array
    {
        return [];
    }

    /**
     * todo unrealized method
     *
     * @param string $key
     *
     * @return array
     */
    public function graterThanOrEqual(string $key): array
    {
        return [];
    }

    /**
     * todo unrealized method
     *
     * @param string $form
     * @param string $to
     *
     * @return array
     */
    public function between(string $form, string $to): array
    {
        return [];
    }
}
