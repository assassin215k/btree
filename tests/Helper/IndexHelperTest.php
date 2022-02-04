<?php

namespace Btree\Test\Helper;

use Btree\Helper\IndexHelper;
use PHPUnit\Framework\TestCase;

/**
 * Test of IndexHelperTest
 *
 * @package assassin215k/btree
 */
class IndexHelperTest extends TestCase
{
    public function testGetIndexName()
    {
        $index = IndexHelper::getIndexName(['name','age']);
        $this->assertSame('name-age', $index);
        $this->assertSame('K-', IndexHelper::DATA_PREFIX);
        $this->assertSame('_', IndexHelper::NULL);
    }
}
