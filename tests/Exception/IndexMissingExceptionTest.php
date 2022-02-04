<?php

namespace Btree\Test\Exception;

use Btree\Exception\IndexMissingException;
use PHPUnit\Framework\TestCase;

/**
 * Test of IndexMissingException
 *
 * @package assassin215k/btree
 */
class IndexMissingExceptionTest extends TestCase
{

    /**
     * @return void
     */
    public function testConstruct()
    {
        $exception = new IndexMissingException('age');

        $this->assertSame("Index 'age' is missed", $exception->getMessage());
    }
}
