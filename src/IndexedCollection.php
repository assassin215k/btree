<?php

namespace Btree;

use Btree\Builder\Builder;
use Btree\Builder\BuilderInterface;
use Btree\Exception\IndexDuplicationException;
use Btree\Exception\IndexMissingException;
use Btree\Helper\IndexHelper;
use Btree\Index\Btree\Index;
use Btree\Index\Btree\IndexInterface;

/**
 * Class IndexedCollection
 *
 * The main class of btree
 *
 * @package assassin215k/btree
 */
class IndexedCollection implements IndexedCollectionInterface
{
    public static string $defaultIndexClass = Index::class;
    public static string $defaultBuilderClass = Builder::class;

    /**
     * @var IndexInterface[]
     */
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
     * @throws IndexDuplicationException
     *
     * @param string|array $fieldName
     * @param IndexInterface|null $index
     */
    public function addIndex(string | array $fieldName, IndexInterface $index = null): void
    {
        $indexKey = IndexHelper::getIndexName($fieldName);
        if (key_exists($indexKey, $this->indexes)) {
            throw new IndexDuplicationException($indexKey);
        }

        if (!$index) {
            $index = new self::$defaultIndexClass($fieldName);
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

    public function add(object $item): void
    {
        $this->data[] = $item;
        foreach ($this->indexes as $index) {
            $index->insert($item);
        }
    }

    public function printFirstIndex(): void
    {
        if (array_key_first($this->indexes)) {
            $this->indexes[array_key_first($this->indexes)]->printTree();
        }
    }

    public function delete(string $key): void
    {
        foreach ($this->indexes as $index) {
            $index->delete($key);
        }
    }

    /**
     * @return BuilderInterface
     */
    public function createBuilder(): BuilderInterface
    {
        return new self::$defaultBuilderClass();
    }
}
