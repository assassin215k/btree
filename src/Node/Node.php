<?php

namespace Btree\Node;

/**
 * Class Node
 *
 * @package assassin215k/btree
 */
class Node implements NodeInterface
{
    private array $children = [];
    private ?NodeInterface $parent;
}
