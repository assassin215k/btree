<?php

namespace Btree\Test\Exception;

use Btree\Exception\IndexDuplicationException;
use PHPUnit\Framework\TestCase;

/**
 * Test of IndexDuplicationException
 *
 * @package assassin215k/btree
 */
class IndexDuplicationExceptionTest extends TestCase
{

    /**
     * @return void
     */
    public function testConstruct()
    {
        $exception = new IndexDuplicationException('age');

        $this->assertSame("Index 'age' already exist", $exception->getMessage());
    }
}
