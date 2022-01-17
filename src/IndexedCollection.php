<?php

namespace Btree;

use Btree\Algorithm\AlgorithmInterface;
use Btree\Algorithm\IndexAlgorithm;
use Btree\Exception\IndexDuplicationException;
use Btree\Exception\IndexMissingException;
use Btree\Exception\WrongClassException;
use Btree\Helper\Index;
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
     * @var Index[]
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
    public function __construct(private readonly array $data)
    {
    }

    /**
     * @param string|array $fieldName
     * @param IndexAlgorithm $algorithm
     * @param bool $replace
     *
     * @throws IndexDuplicationException
     * @throws WrongClassException
     */
    public function addIndex(
        string|array $fieldName,
        IndexAlgorithm $algorithm = IndexAlgorithm::BTREE,
        bool $replace = true
    ): void {
        $algorithmClass = IndexAlgorithm::getAlgorithm($algorithm);
        $algorithm = new $algorithmClass();

        if (!$algorithm instanceof AlgorithmInterface) {
            throw new WrongClassException($algorithm::class);
        }

        $indexKey = Index::getIndex($fieldName);

        if (!$replace && key_exists($indexKey, $this->indexes)) {
            throw new IndexDuplicationException($indexKey);
        }

        $this->indexes[$indexKey] = $algorithm->createIndex($fieldName);

        foreach ($this->data as $item) {
            $algorithm->addItem($item);
        }
    }

    /**
     * @param string|array $fieldName
     *
     * @return void
     *
     * @throws IndexMissingException
     */
    public function dropIndex(string|array $fieldName): void
    {
        $indexKey = Index::getIndex($fieldName);

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

    public function add(object $data): void
    {
    }
}
