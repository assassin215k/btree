<?php

namespace Assassin215k\Btree\Test;

use Assassin215k\Btree\Btree;
use PHPUnit\Framework\TestCase;

/**
 * Class BtreeTest
 *
 * @package assassin215k/btree
 */
class BtreeTest extends TestCase
{
    /**
     * Test that true does in fact equal true
     */
    public function testTrueIsTrue()
    {
        $btree = new Btree();
        $this->assertSame('testme', $btree->echoPhrase('testme'));
        $this->assertTrue(true);
    }
}
