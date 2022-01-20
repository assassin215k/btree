<?php

namespace Btree\Index;

use Btree\Exception\MissedFieldException;
use Btree\Exception\MissedPropertyException;
use Btree\Node\Node;

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

        $this->root = new Node(self::$nodeSize);
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

        $this->root = $this->root->insertKey($key, $value);
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
        if (!is_null($this->root)) {
            $this->root->traverse();
        }
    }
}
