<?php

namespace Btree\Helper;

/**
 * Class Index
 *
 * Helpers for indexes
 *
 * @package assassin215k/btree
 */
class Index
{
    public static function getIndex(string|array $fieldName): string
    {
        return is_array($fieldName) ? join('-', $fieldName) : $fieldName;
    }
}
