<?php

namespace Btree;

use Btree\Algorithm\IndexAlgorithm;
use Btree\Exception\IndexDuplicationException;
use Btree\Exception\IndexMissingException;
use Btree\Exception\WrongClassException;
use Btree\Helper\IndexHelper;
use Btree\Index\IndexInterface;
use Btree\SortOrder\IndexSortOrder;

/**
 * Class IndexedCollection
 *
 * The main class of btree
 *
 * @package assassin215k/btree
 */
class IndexedCollection implements IndexedCollectionInterface
{
    /**
     * @var IndexInterface[]
     */
    private array $indexes = [];

    /**
     * @var array of pare key => order
     */
    private array $sortOrder = [];

    /**
     * Create a new collection Instance
     *
     * @param array $data to store original data
     */
    public function __construct(private array $data)
    {
    }

    /**
     * @throws IndexDuplicationException
     * @throws WrongClassException
     *
     * @param bool $replace
     *
     * @param string|array $fieldName
     * @param IndexAlgorithm $algorithm
     */
    public function addIndex(string | array $fieldName, IndexAlgorithm $algorithm = IndexAlgorithm::BTREE): void
    {
        $indexKey = IndexHelper::getIndexName($fieldName);
        if (key_exists($indexKey, $this->indexes)) {
            throw new IndexDuplicationException($indexKey);
        }

        $indexClass = IndexAlgorithm::getIndexClass($algorithm);
        $index = new $indexClass($fieldName);

        if (!$index instanceof IndexInterface) {
            throw new WrongClassException($algorithm::class);
        }

        $this->indexes[$indexKey] = $index;

        foreach ($this->data as $item) {
            $index->insert($item);
        }
    }

    /**
     * @throws IndexMissingException
     *
     * @param string|array $fieldName
     *
     * @return void
     *
     */
    public function dropIndex(string | array $fieldName): void
    {
        $indexKey = IndexHelper::getIndexName($fieldName);

        if (!key_exists($indexKey, $this->indexes)) {
            throw new IndexMissingException($indexKey);
        }

        unset($this->indexes[$indexKey]);
    }

    /**
     * @param string $field
     * @param IndexSortOrder $order
     *
     * @return $this
     */
    public function sortBy(string $field, IndexSortOrder $order): self
    {
        $this->sortOrder = [$field => $order];

        return $this;
    }

    /**
     * @param string $field
     * @param IndexSortOrder $order
     *
     * @return $this
     */
    public function addSortBy(string $field, IndexSortOrder $order): self
    {
        $this->sortOrder = array_merge($this->sortOrder, [$field => $order]);

        return $this;
    }

    public function search(array $where): array
    {
        return [];
    }

    public function add(object $item): void
    {
        $this->data[] = $item;
        foreach ($this->indexes as $index) {
            $index->insert($item);
        }
    }

    public function printFirstIndex(): void
    {
        if (count($this->indexes)) {
            array_shift($this->indexes)->printTree();
        }
    }
}
