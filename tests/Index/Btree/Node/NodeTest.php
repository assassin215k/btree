<?php
/**
 * Created by PhpStorm.
 * Author: Ihor Fedan
 * Date: 25.01.22
 * Time: 11:14
 */

namespace Btree\Test\Index\Btree\Node;

use Btree\Index\Btree\Node\Data\DataInterface;
use Btree\Index\Btree\Node\Node;
use Btree\Index\Btree\Node\NodeInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Test for Class Node
 *
 * @package assassin215k/btree
 */
class NodeTest extends TestCase
{
    protected NodeInterface $node;
    protected DataInterface $data;

    public function setUp(): void
    {
        $this->data = Mockery::mock(DataInterface::class);
        $this->data->shouldReceive('add');

        $keys = [
            'N3' => new Node(),
            'K2' => $this->data,
            'N2' => new Node(),
            'K1' => $this->data,
            'N1' => new Node(),
        ];

        $this->node = new Node(false, $keys, 2, 3);
    }

    public function testConstruct()
    {
        $node = new Node();
        $this->assertTrue($node->isLeaf());
        $this->assertSame(0, $node->keyTotal);
        $this->assertSame(0, count($node->getKeys()));

        $node = new Node(false, [1, 2, 3], 3);
        $this->assertFalse($node->isLeaf());
        $this->assertSame(3, $node->keyTotal);
        $this->assertSame(3, count($node->getKeys()));
    }

    public function testGetNodeByKey()
    {
        $this->assertInstanceOf($this->node::class, $this->node->getNodeByKey('N1'));

        $this->expectError();
        $this->node->getNodeByKey('K2');
    }

    public function testGetKeys()
    {
        $keys = $this->node->getKeys();

        $this->assertSame(5, count($keys));
        $this->assertInstanceOf(NodeInterface::class, $keys['N1']);
        $this->assertInstanceOf(DataInterface::class, $keys['K2']);
    }

    public function testReplaceKey()
    {
        $node = $this->node;
        $array = [
            'N11' => new Node(),
            'K11' => $this->data,
            'N22' => new Node(),
        ];

        $node->replaceKey($array, fullReplace: true);
        $key = $node->getKeys();

        $this->assertSame(1, $node->keyTotal);
        $this->assertSame(2, $node->nodeTotal);

        $this->assertInstanceOf(NodeInterface::class, $key['N11']);
        $this->assertInstanceOf(DataInterface::class, $key['K11']);
        $this->assertInstanceOf(NodeInterface::class, $key['N22']);

        $array = [
            'N111' => new Node(),
            'K111' => $this->data,
            'N222' => new Node(),
        ];

        $node->replaceKey($array, 'N22');
        $key = $node->getKeys();

        $this->assertSame(2, $node->keyTotal);
        $this->assertSame(3, $node->nodeTotal);

        $this->assertInstanceOf(NodeInterface::class, $key['N11']);
        $this->assertInstanceOf(DataInterface::class, $key['K11']);
        $this->assertInstanceOf(NodeInterface::class, $key['N111']);
        $this->assertInstanceOf(DataInterface::class, $key['K111']);
        $this->assertInstanceOf(NodeInterface::class, $key['N222']);
    }

    public function testIsLeaf()
    {
        $this->assertFalse($this->node->isLeaf());
    }

    public function testSetLeaf()
    {
        $this->node->setLeaf(true);
        $this->assertTrue($this->node->isLeaf());
    }

    public function testCount()
    {
        $this->assertSame(2, $this->node->count());
    }

    public function testSplitKeys()
    {
        $keys = [
            'K4' => $this->data,
            'K3' => $this->data,
            'K2' => $this->data,
            'K1' => $this->data,
        ];

        $node = new Node(keys: $keys, keyTotal: 4);
        $keys = $node->splitKeys(2);
        $this->assertSame(2, count($keys));
        $this->assertSame(2, count($node->getKeys()));
        $this->assertSame(2, $node->keyTotal);

        $keys = [
            'K9' => $this->data,
            'K8' => $this->data,
            'K7' => $this->data,
            'K6' => $this->data,
            'K5' => $this->data,
            'K4' => $this->data,
            'K3' => $this->data,
            'K2' => $this->data,
            'K1' => $this->data,
        ];

        $node = new Node(keys: $keys, keyTotal: 9);
        $keys = $node->splitKeys(5);
        $this->assertSame(4, count($keys));
        $this->assertSame(5, $node->keyTotal);
    }

    public function testExtractLast()
    {
        $array = $this->node->extractLast();
        $this->assertSame('N1', array_key_first($array));
        $this->assertInstanceOf(NodeInterface::class, array_pop($array));

        $array = $this->node->extractLast();
        $array = $this->node->extractLast();
        $array = $this->node->extractLast();
        $array = $this->node->extractLast();

        $this->assertSame(0, $this->node->keyTotal);
    }

    public function testGetChildNodeKey()
    {
        $node = $this->node;

        $this->assertSame(2, $node->keyTotal);
        $this->assertSame(3, $node->nodeTotal);
        $this->assertSame('N2', $node->getChildNodeKey('K111'));

        $array = [
            'N12' => new Node(),
            'K11' => $this->data,
            'N11' => new Node(),
        ];

        $node->replaceKey($array, 'N2');
        $this->assertSame(3, $node->keyTotal);
        $this->assertSame(4, $node->nodeTotal);
        $this->assertSame('N12', $node->getChildNodeKey('K111'));

        $array = [
            'N112' => new Node(
                false,
                [
                    'K1113' => $this->data,
                    'K1112' => $this->data,
                    'K1111' => $this->data,
                ],
                3
            ),
            'K111' => $this->data,
            'N111' => new Node(),
        ];

        $node->replaceKey($array, 'N12');
        $this->assertSame(4, $node->keyTotal);
        $this->assertSame(5, $node->nodeTotal);
        $this->assertSame('N112', $node->getChildNodeKey('K1112'));

        $this->assertSame('N3', $node->getChildNodeKey('K333'));
        $this->assertSame('N11', $node->getChildNodeKey('K0'));

        $node = new Node(false, ['K1' => new Node()], 0, 1);
        $this->assertSame('K1', $node->getChildNodeKey('K1'));
    }

    public function testInsertKey()
    {
        $keys = [
            'K4' => $this->data,
            'K3' => $this->data,
            'K2' => $this->data,
            'K1' => $this->data,
        ];

        $node = new Node(keys: $keys, keyTotal: 4);

        $node->insertKey('K31', $this->data, 1);
        $keys = array_keys($node->getKeys());
        $this->assertSame(['K4', 'K31', 'K3', 'K2', 'K1'], $keys);

        $node->insertKey('K10', $this->data, 4);
        $keys = array_keys($node->getKeys());
        $this->assertSame(['K4', 'K31', 'K3', 'K2', 'K10', 'K1'], $keys);

        $node->insertKey('K0', $this->data, 6);
        $keys = array_keys($node->getKeys());
        $this->assertSame(['K4', 'K31', 'K3', 'K2', 'K10', 'K1', 'K0'], $keys);

        $node->insertKey('K20', $this->data);
        $keys = array_keys($node->getKeys());
        $this->assertSame(['K4', 'K31', 'K3', 'K20', 'K2', 'K10', 'K1', 'K0'], $keys);

        $node->insertKey('K', $this->data);
        $keys = array_keys($node->getKeys());
        $this->assertSame(['K4', 'K31', 'K3', 'K20', 'K2', 'K10', 'K1', 'K0', 'K'], $keys);

        $node->insertKey('K', $this->data);
        $keys = array_keys($node->getKeys());
        $this->assertSame(['K4', 'K31', 'K3', 'K20', 'K2', 'K10', 'K1', 'K0', 'K'], $keys);
    }
}
