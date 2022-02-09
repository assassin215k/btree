<?php

namespace Btree;

use Btree\Builder\Builder;
use Btree\Builder\BuilderInterface;
use Btree\Exception\IndexDuplicationException;
use Btree\Exception\IndexMissingException;
use Btree\Exception\InvalidIndexClassException;
use Btree\Helper\IndexHelper;
use Btree\Index\Btree\Index;
use Btree\Index\IndexInterface;

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
    public function __construct(private array $data = [], private readonly array $options = [])
    {
        if (key_exists('builderClass', $this->options)) {
            self::$defaultBuilderClass = $this->options['builderClass'];
        }

        if (key_exists('indexClass', $this->options)) {
            self::$defaultIndexClass = $this->options['indexClass'];
        }
    }

    /**
     * @throws IndexDuplicationException
     * @throws InvalidIndexClassException
     *
     * @param string|array $fieldName
     * @param IndexInterface|null $index
     */
    public function addIndex(string | array $fieldName, IndexInterface $index = null): void
    {
        if (!$index) {
            $index = new self::$defaultIndexClass($fieldName);

            if (!$index instanceof IndexInterface) {
                throw new InvalidIndexClassException();
            }
        }

        $indexKey = IndexHelper::getIndexName($fieldName);
        if (key_exists($indexKey, $this->indexes)) {
            throw new IndexDuplicationException($indexKey);
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
     * @param object $item
     *
     * @return void
     */
    public function add(object $item): void
    {
        $this->data[] = $item;
        foreach ($this->indexes as $index) {
            $index->insert($item);
        }
    }

    /**
     * @return string|null
     */
    public function printFirstIndex(): ?string
    {
        if (array_key_first($this->indexes)) {
            return $this->indexes[array_key_first($this->indexes)]->printTree();
        }

        return null;
    }

    /**
     * @param string|object|array $key
     *
     * @return void
     */
    public function delete(string | object | array $key): void
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
        return new self::$defaultBuilderClass($this->indexes, $this->data);
    }
}
