<?php

namespace Btree\Index\Btree;

use Btree\Exception\MissedFieldException;
use Btree\Exception\MissedPropertyException;
use Btree\Index\Btree\Node\Node;
use Btree\Index\Btree\Node\NodeInterface;

/**
 * Class Index
 *
 * Index to contain index cache
 *
 * @package assassin215k/btree
 */
class Index implements IndexInterface
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
     * @param int $nodeSize
     * @param array|string $fields
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

        $this->root = new Node(self::$nodeSize, true);
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

        $this->root->searchLeaf($key)->insertKey($key, $value);
        $this->root = $this->root->getRoot();
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

            $key .= is_null($value->$field) ? '_' : $value->$field;
        }

        return $key;
    }

    /**
     * @param string $key
     *
     * @return void
     */
    public function delete(string $key): void
    {
        $this->root->searchNode($key)->dropKey($key);

        $this->root = $this->root->getRoot();
        $this->root->parent = null;
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function search(string $key): array
    {
        $node = $this->root->searchNode($key);

        return $node->selectKey($key);
    }

    public function printTree(): void
    {
        $this->root->traverse();
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
