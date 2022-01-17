<?php

namespace Btree\Index;

use Btree\Algorithm\AlgorithmInterface;
use Btree\Node\Node;

/**
 * Class Index
 *
 * Index to contain index cache
 *
 * @package assassin215k/btree
 */
class Index implements IndexInterface
{
    private mixed $cache;

    public function __construct(private readonly array $fields, private readonly AlgorithmInterface $algorithm)
    {
    }

    public function initCache(){
        $this->cache = $this->algorithm->getCache();
    }

    public function add(object $value): void
    {
        $this->algorithm->addItem($this->cache, $this->fields, $value);
    }

    public function search(string $value): array
    {
        return [];
    }
}
