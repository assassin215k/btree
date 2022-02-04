<?php

namespace Btree\Test\Builder\Exception;

use Btree\Builder\Exception\EmptyFieldException;
use PHPUnit\Framework\TestCase;

/**
 * Test of InvalidConditionValueException
 *
 * @package assassin215k/btree
 */
class EmptyFieldExceptionTest extends TestCase
{

    /**
     * @return void
     */
    public function testConstruct()
    {
        $exception = new EmptyFieldException();

        $this->assertSame("Condition field name is not specified", $exception->getMessage());
    }
}
