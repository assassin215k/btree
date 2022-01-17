<?php

namespace Btree;

use Btree\Algorithm\AlgorithmInterface;
use Btree\Algorithm\IndexAlgorithm;
use Btree\Exception\IndexDuplicationException;
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
    private array $indexes = [];

    /**
     * Create a new collection Instance
     *
     * @param array $data to store original data
     */
    public function __construct(private array $data)
    {
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param string|array $fieldName
     * @param IndexAlgorithm $algorithm
     * @param bool $replace
     *
     * @throws IndexDuplicationException
     * @throws WrongClassException
     */
    public function addIndex(string|array $fieldName, IndexAlgorithm $algorithm, bool $replace = true): void
    {
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
    }

    public function sortBy(string $field, IndexSortOrder $order): self
    {
        return $this;
    }

    public function search(array $where): array
    {
        return [];
    }
}
