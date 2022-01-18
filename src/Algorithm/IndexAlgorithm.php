<?php

namespace Btree\Algorithm;

use Btree\Index\BtreeIndex;

/**
 * Enum IndexAlgorithm
 *
 * Base enumeration of algorithm
 *
 * @package assassin215k/btree
 */
enum IndexAlgorithm
{
    case BTREE;

    public static function getIndexClass(self $value): string
    {
        return match ($value) {
            IndexAlgorithm::BTREE => BtreeIndex::class,
        };
    }
}
