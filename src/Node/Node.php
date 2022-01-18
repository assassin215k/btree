<?php

namespace Btree\Node;

use Countable;

/**
 * Class Node
 *
 * @package assassin215k/btree
 */
class Node implements NodeInterface
{
    public ?NodeInterface $prevNode = null;
    public ?NodeInterface $nextNode = null;
    public ?NodeInterface $parent = null;

    public array $list;

    /**
     * @var NodeInterface[]|null
     */
    private ?array $children = null;

    /**
     * @return int
     */
    public function isList(): int
    {
        return count($this->list) > 1;
    }
}
