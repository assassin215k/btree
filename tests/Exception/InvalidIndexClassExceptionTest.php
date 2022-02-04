<?php

namespace Btree\Test\Exception;

use Btree\Exception\InvalidIndexClassException;
use PHPUnit\Framework\TestCase;

/**
 * Test of InvalidIndexClassException
 *
 * @package assassin215k/btree
 */
class InvalidIndexClassExceptionTest extends TestCase
{

    /**
     * @return void
     */
    public function testConstruct()
    {
        $exception = new InvalidIndexClassException();

        $this->assertSame("Specified invalid class", $exception->getMessage());
    }
}
