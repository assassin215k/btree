<?php

namespace Btree\Algorithm;

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

    public static function getAlgorithm(self $value): string
    {
        return match ($value) {
            IndexAlgorithm::BTREE => Btree::class,
        };
    }
}
