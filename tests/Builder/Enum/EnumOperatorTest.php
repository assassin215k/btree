<?php

namespace Btree\Test\Builder\Enum;

use Btree\Builder\Enum\EnumOperator;
use PHPUnit\Framework\TestCase;

/**
 * Test of EnumOperator
 *
 * @package assassin215k/btree
 */
class EnumOperatorTest extends TestCase
{
    public function testEnum()
    {
        $cases = EnumOperator::cases();
        $this->assertSame(7, count($cases));
    }
}
