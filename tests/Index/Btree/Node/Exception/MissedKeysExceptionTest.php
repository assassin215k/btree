<?php

namespace Btree\Test\Index\Btree\Node\Exception;

use Btree\Index\Btree\Node\Exception\MissedKeysException;
use PHPUnit\Framework\TestCase;

/**
 * Test of MissedKeysExceptionTest
 *
 * @package assassin215k/btree
 */
class MissedKeysExceptionTest extends TestCase
{

    /**
     * @return void
     */
    public function testConstruct()
    {
        $exception = new MissedKeysException();

        $this->assertSame('No keys found in the Node', $exception->getMessage());
    }
}
