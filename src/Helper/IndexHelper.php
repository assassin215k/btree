<?php

namespace Btree\Helper;

/**
 * Class IndexHelper
 *
 * Helpers for indexes
 *
 * @package assassin215k/btree
 */
class IndexHelper
{
    public const DATA_PREFIX = 'K-';
    public const NULL = '_';

    public static function getIndexName(string|array $fieldName): string
    {
        return is_array($fieldName) ? join('-', $fieldName) : $fieldName;
    }
}
