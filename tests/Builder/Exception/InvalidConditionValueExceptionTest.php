<?php

namespace Btree\Test\Builder\Exception;

use Btree\Builder\Exception\InvalidConditionValueException;
use PHPUnit\Framework\TestCase;

/**
 * Test of InvalidConditionValueException
 *
 * @package assassin215k/btree
 */
class InvalidConditionValueExceptionTest extends TestCase
{

    /**
     * @return void
     */
    public function testConstruct()
    {
        $exception = new InvalidConditionValueException();

        $this->assertSame("Invalid value(s) for condition operation!", $exception->getMessage());
    }
}
