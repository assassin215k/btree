<?php

namespace Btree\Builder\Enum;

/**
 * Enum EnumOperator
 *
 * Available operations
 *
 * @package assassin215k/btree
 */
enum EnumOperator
{
    case Equal;
    case LessThen;
    case LessThenOrEqual;
    case GreaterThen;
    case GreaterThenOrEqual;
    case Between;
    case IsNull;
}
