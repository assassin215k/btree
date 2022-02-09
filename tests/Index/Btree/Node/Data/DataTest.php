<?php

namespace Btree\Test\Index\Btree\Node\Data;

use Btree\Index\Btree\Node\Data\Data;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Test for Class Data
 *
 * @package assassin215k/btree
 */
class DataTest extends TestCase
{
    protected Data $data;

    public function testTotal()
    {
        $this->assertSame(1, $this->data->total());
    }

    public function testAdd()
    {
        $this->data->add(new DateTime());
        $this->data->add(new DateTime());

        $this->assertSame(3, $this->data->total());
    }

    public function testGet()
    {
        $this->assertSame(1, count($this->data->get()));
    }

    protected function setUp(): void
    {
        $this->data = new Data(new DateTime());
    }
}
