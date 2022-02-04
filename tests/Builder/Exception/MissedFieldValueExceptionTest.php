<?php

namespace Btree\Test\Builder\Exception;

use Btree\Builder\Exception\MissedFieldValueException;
use PHPUnit\Framework\TestCase;

/**
 * Test of InvalidConditionValueException
 *
 * @package assassin215k/btree
 */
class MissedFieldValueExceptionTest extends TestCase
{

    /**
     * @return void
     */
    public function testConstruct()
    {
        $exception = new MissedFieldValueException();

        $this->assertSame("Condition is required a value!", $exception->getMessage());
    }
}
