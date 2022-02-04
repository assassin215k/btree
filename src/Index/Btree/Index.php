<?php

namespace Btree\Index\Btree;

use Btree\Helper\IndexHelper;
use Btree\Index\Btree\Node\Data\DataInterface;
use Btree\Index\Btree\Node\Node;
use Btree\Index\Btree\Node\NodeInterface;
use Btree\Index\Exception\MissedFieldException;
use Btree\Index\Exception\MissedPropertyException;

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

    private ?NodeInterface $root = null;

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
            if (!strlen($field)) {
                throw new MissedFieldException();
            }
        }

        $this->degree = self::$nodeSize;

        $this->root = new Node();
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
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
    private function getKey(string | object | array $value): string
    {
        $key = IndexHelper::DATA_PREFIX;

        if (is_string($value)) {
            return $key . $value;
        }

        if (is_array($value)) {
            foreach ($this->fields as $field) {
                if (!array_key_exists($field, $value)) {
                    throw new MissedPropertyException($field, $value);
                }

                $key .= is_null($value[$field]) ? IndexHelper::NULL : $value[$field];
            }

            return $key;
        }

        foreach ($this->fields as $field) {
            if (!property_exists($value, $field)) {
                throw new MissedPropertyException($field, $value);
            }

            $key .= is_null($value->$field) ? IndexHelper::NULL : $value->$field;
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
            unset($child);
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
        $keyTotal = intdiv($node->keyTotal, 2);
        $nodeTotal = $node->nodeTotal / 2;
        $position = $nodeTotal + (int)ceil($node->keyTotal / 2);
        $keys = $node->getKeys();

        $newKeys = array_splice($keys, $position);

        $nextNode = new Node($node->isLeaf(), $newKeys, $keyTotal, $nodeTotal);

        /** @var DataInterface $value */
        $medianKey = array_key_last($keys);
        $medianValue = array_pop($keys);

        $prevNode = new Node($node->isLeaf(), $keys, $keyTotal, $nodeTotal);
        $prevNode->nextNode = $nextNode;
        $nextNode->prevNode = $prevNode;

        if ($node->nextNode) {
            $nextNode->nextNode = $node->nextNode;
            $node->nextNode->prevNode = $nextNode;
        }

        if ($node->prevNode) {
            $prevNode->prevNode = $node->prevNode;
            $node->prevNode->nextNode = $prevNode;
        }

        /**
         * All nodes key start from N< or N>
         * All Keys start from K-
         */
        $lKey = substr($medianKey, 2);

        return [
            "N<$lKey" => $prevNode,
            $medianKey => $medianValue,
            "N>$lKey" => $nextNode
        ];
    }

    /**
     * @throws MissedPropertyException
     *
     * @param string|object|array $target
     *
     * @return bool false if key is not found
     */
    public function delete(string | object | array $target): bool
    {
        $target = $this->getKey($target);

        $success = (bool)$this->deleteFromNode($this->root, $target);

        if ($this->root->isLeaf()) {
            return $success;
        }

        if ($this->root->nodeTotal() === 1) {
            $children = $this->root->getKeys();

            $this->root = array_pop($children);
        }

        return $success;
    }

    /**
     * Delete key recursively
     *
     * @param NodeInterface $node
     * @param string $key
     *
     * @return bool | DataInterface
     */
    private function deleteFromNode(NodeInterface $node, string $key): bool | DataInterface
    {
        if ($node->isLeaf()) {
            if ($node->hasKey($key)) {
                return $node->dropKey($key);
            }

            return false;
        }

        if ($node->hasKey($key)) {
            return $this->dropKeyFromNotLeaf($node, $key);
        }

        $position = $node->getChildNodeKey($key);
        $child = $node->getNodeByKey($position);

        $deletedKey = $this->deleteFromNode($child, $key);
        if (!$deletedKey) {
            return false;
        }

        if ($child->count() < $this->degree) {
            $this->rebaseChildren($node, $child, $position);
        }

        return $deletedKey;
    }

    /**
     * Drop key between two nodes
     *
     * @param NodeInterface $node
     * @param string $key
     *
     * @return DataInterface
     */
    private function dropKeyFromNotLeaf(NodeInterface $node, string $key): DataInterface
    {
        $children = $node->getKeys();
        $keys = array_keys($children);
        $keyIndexes = array_flip($keys);

        $toRemove = $children[$key];

        /** @var NodeInterface $child */
        $child = $children[$keys[$keyIndexes[$key] + 1]];
        $childChildren = $child->getKeys();
        $firstKey = array_key_first($childChildren);

        if ($childChildren[$firstKey] instanceof NodeInterface) {
            $firstKey = array_slice(array_keys($childChildren), $keyIndexes[$key] - 1, 1)[0];
        }

        $value = [$firstKey => $this->deleteFromNode($child, $firstKey)];

        $node->replaceKey($value, $key, keyOnly: true);

        if ($child->count() < $this->degree) {
            $position = array_key_first(array_slice($keyIndexes, $keyIndexes[$key] + 1, 1));
            $this->rebaseChildren($node, $child, $position);
        }

        return $toRemove;
    }

    /**
     * Rebase a child of the node if the child has not enough keys
     *
     * @param NodeInterface $node
     * @param NodeInterface $child
     * @param string $position
     *
     * @return void
     */
    private function rebaseChildren(NodeInterface $node, NodeInterface $child, string $position): void
    {
        $prevNode = $child->getPrevNode();
        if ($prevNode && $prevNode->count() >= $this->degree) {
            $node->replaceNextPrevKey($child, true);

            return;
        }

        $nextNode = $child->getNextNode();
        if ($nextNode && $nextNode->count() >= $this->degree) {
            $node->replaceNextPrevKey($child, false);

            return;
        }

        $nodeKeys = array_flip(array_keys($node->getKeys()));
        if ($nextNode) {
            $prevK = array_slice($node->getKeys(), $nodeKeys[$position] + 1, 1, preserve_keys: true);

            $keyTotal = $child->count() + $nextNode->count() + 1;
            $nodeTotal = $child->nodeTotal() + $nextNode->nodeTotal();

            $keys = $child->getKeys() + $prevK + $nextNode->getKeys();
            $newNode = new Node(isLeaf: $child->isLeaf(), keys: $keys, keyTotal: $keyTotal, nodeTotal: $nodeTotal);
            $newNode->setNextNode($nextNode->getNextNode());
            $nextNode->getNextNode()?->setPrevNode($newNode);
            $newNode->setPrevNode($child->getPrevNode());
            $child->getPrevNode()?->setNextNode($newNode);

            $node->replaceThreeWithOne($position, $newNode, $nodeKeys, true);

            return;
        }

        // Use prev Node
        $nextK = array_slice($node->getKeys(), $nodeKeys[$position] - 1, 1, preserve_keys: true);

        $keyTotal = $child->count() + $prevNode->count() + 1;
        $nodeTotal = $child->nodeTotal() + $prevNode->nodeTotal();

        $keys = $prevNode->getKeys() + $nextK + $child->getKeys();
        $newNode = new Node(isLeaf: $child->isLeaf(), keys: $keys, keyTotal: $keyTotal, nodeTotal: $nodeTotal);
        $newNode->setPrevNode($prevNode->getPrevNode());
        $prevNode->getPrevNode()?->setNextNode($newNode);

        $node->replaceThreeWithOne($position, $newNode, $nodeKeys, false);
    }

    /**
     * @param string $key
     * @param NodeInterface|null $node
     *
     * @return array
     */
    public function search(string $key, NodeInterface $node = null): array
    {
        if (!$node) {
            $node = $this->root;
        }

        if ($node->hasKey($key) || $node->isLeaf()) {
            /** @var DataInterface|null $data */
            $data = $node->getKey($key);

            return $data?->get() ?? [];
        }

        $childKey = $node->getChildNodeKey($key);
        $child = $node->getKey($childKey);

        if (is_null($child)) {
            $child = array_slice($node->getKeys(), -1, 1);
        }

        return $this->search($key, $child);
    }

    /**
     * @param NodeInterface|null $node
     * @param int $level
     * @param string $key
     *
     * @return string
     */
    public function printTree(NodeInterface $node = null, int $level = 0, string $key = ''): string
    {
        $level++;
        $tree = '';

        if (is_null($node)) {
            $node = $this->root;
            $tree .= PHP_EOL;
        }

        $tree .= str_pad('', $level * 5, '_') . $key . PHP_EOL;

        foreach ($node->getKeys() as $key => $item) {
            if ($item instanceof NodeInterface) {
                $tree .= $this->printTree($item, $level, $key);
            }
        }

        return $tree;
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function lessThan(string $key): array
    {
        return $this->extract($this->searchRange(from: $key));
    }

    /**
     * @param array $dataArray
     *
     * @return array
     */
    private function extract(array $dataArray): array
    {
        /** @var DataInterface $data */
        return array_reduce($dataArray, function (?array $carry, $data) {
            if (is_null($carry)) {
                $carry = [];
            }

            foreach ($data->get() as $item) {
                $carry[] = $item;
            }

            return $carry;
        });
    }

    /**
     * @param string|null $from
     * @param bool $fromInclude
     * @param string|null $to
     * @param bool $toInclude
     * @param NodeInterface|null $node
     *
     * @return object[]
     */
    private function searchRange(
        string $from = null,
        bool $fromInclude = false,
        string $to = null,
        bool $toInclude = false,
        NodeInterface $node = null
    ): array {
        if (is_null($node)) {
            $node = $this->root;
        }

        $keys = $node->getKeys();
        $keyList = array_keys($keys);

        $firstKey = self::getFirstKey($keyList, $from, $fromInclude);
        $lastKey = self::getFirstKey($keyList, $to, $fromInclude);

        $flippedKeys = array_flip($keyList);
        $keys = array_slice($keys, !is_null($firstKey) ? $flippedKeys[$firstKey] : array_key_last($keyList));
        if (!is_null($firstKey)) {
            $keyList = array_keys($keys);
            $flippedKeys = array_flip($keyList);
            $keys = array_slice($keys, 0, $flippedKeys[$lastKey] + 1);
        }

        if ($node->isLeaf()) {
            return $keys;
        }

        $result = [];
        foreach ($keys as $key => $child) {
            if ($child instanceof DataInterface) {
                if (strnatcmp($key, $from) > 0 || !$fromInclude && $key === $from) {
                    continue;
                }

                if (strnatcmp($key, $to) < 0 || !$toInclude && $key === $to) {
                    continue;
                }

                $result[$key] = $child;
                continue;
            }

            $result = array_merge($result, $this->searchRange($from, $fromInclude, $to, $toInclude, $child));
        }

        return $result;
    }

    /**
     * @param array $keys
     * @param string|null $key
     * @param bool $include
     *
     * @return string|null
     */
    public static function getFirstKey(array $keys, string $key = null, bool $include = false): ?string
    {
        if (is_null($key)) {
            return array_key_first($keys);
        }

        $filtered = array_filter($keys, function ($k) use ($key, $include) {
            if (!str_starts_with($k, IndexHelper::DATA_PREFIX)) {
                return false;
            }

            return $include ? $k === $key && strnatcmp($k, $key) < 0 : strnatcmp($k, $key) < 0;
        });

        if (!count($filtered)) {
            return null;
        }

        $firstKey = $filtered[array_key_first($filtered)];
        if (strnatcmp($firstKey, $key) < 0) {
            $firstKey = $keys[array_flip($keys)[$firstKey] - 1];
        }

        return $firstKey;
    }

    /**
     * @param array $keys
     * @param string|null $key
     * @param bool $include
     *
     * @return string|null
     */
    public static function getLastKey(array $keys, string $key = null, bool $include = false): ?string
    {
        if (is_null($key)) {
            return array_key_last($keys);
        }

        $filtered = array_filter($keys, function ($k) use ($key, $include) {
            if (!str_starts_with($k, IndexHelper::DATA_PREFIX)) {
                return false;
            }

            return $include ? $k === $key && strnatcmp($k, $key) > 0 : strnatcmp($k, $key) > 0;
        });

        if (!count($filtered)) {
            return null;
        }

        $lastKey = $filtered[array_key_last($filtered)];

        if (strnatcmp($lastKey, $key) > 0) {
            $lastKey = $keys[array_flip($keys)[$lastKey] + 1];
        }

        return $lastKey;
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function lessThanOrEqual(string $key): array
    {
        return $this->extract($this->searchRange(from: $key, fromInclude: true));
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function greaterThan(string $key): array
    {
        return $this->extract($this->searchRange(to: $key));
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function greaterThanOrEqual(string $key): array
    {
        return $this->extract($this->searchRange(to: $key, toInclude: true));
    }

    /**
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function between(string $from, string $to): array
    {
        return $this->extract($this->searchRange(from: $from, fromInclude: true, to: $to, toInclude: true));
    }
}
