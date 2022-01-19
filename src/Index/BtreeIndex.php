<?php

namespace Btree\Index;

use Btree\Exception\MissedFieldException;
use Btree\Exception\MissedPropertyException;
use Btree\Node\Node;
use Btree\Node\NodeInterface;
use ReflectionClass;
use ReflectionProperty;

/**
 * Class Index
 *
 * Index to contain index cache
 *
 * @package assassin215k/btree
 */
class BtreeIndex implements IndexInterface
{
    public static int $nodeSize = 16;

    private ?Node $root = null;
    private readonly array|string $fields;

    /**
     * @param array|string $fields
     *
     * @throws MissedFieldException
     */
    public function __construct(array|string $fields)
    {
        self::$nodeSize = 2;

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

    public function insert(object $value): void
    {
        $key = '';
        foreach ($this->fields as $field) {
            if (empty($value->$field)) {
                throw new MissedPropertyException($field, $value);
            }

            $key .= $value->$field;
        }

        var_dump($key);

//        var_dump(get_object_vars($value));
//        var_dump(get_object_vars($value));
//        $key =
//        $hash = $this->encode($value->getName() . $value->getAge());

//        $this->root-> ($hash, $value);
//        var_dump($hash);
//        if ($this->root)
    }

    public function search(string $value): array
    {
        $node = $this->searchNode($value);
        if (is_null($node)) {
            return [];
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
}
