<?php

namespace Btree\Test\Builder\Enum;

use Btree\Builder\Enum\EnumSort;
use PHPUnit\Framework\TestCase;

/**
 * Test of EnumSort
 *
 * @package assassin215k/btree
 */
class EnumSortTest extends TestCase
{
    public function testEnum()
    {
        $cases = EnumSort::cases();
        $this->assertSame(2, count($cases));
    }
}
