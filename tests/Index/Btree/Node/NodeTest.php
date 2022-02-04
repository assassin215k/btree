<?php

namespace Btree\Test\Index\Btree\Node;

use Btree\Index\Btree\Node\Data\Data;
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

    private array $keys;

    public function setUp(): void
    {
        $this->data = Mockery::mock(DataInterface::class);
        $this->data->shouldReceive('add');

        $this->keys = [
            'N3' => new Node(),
            'K2' => $this->data,
            'N2' => new Node(),
            'K1' => $this->data,
            'N1' => new Node(),
        ];

        $this->node = new Node(false, $this->keys, 2, 3);
    }

//    public function testBase()
//    {
//        var_dump(array_keys($this->node->getKeys()));
//        die;
//    }

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

    public function testNeighbours()
    {
        $node = new Node();
        $prev = new Node();
        $next = new Node();

        $node->setPrevNode($prev);
        $node->setNextNode($next);

        $this->assertSame($prev, $node->getPrevNode());
        $this->assertSame($next, $node->getNextNode());
    }

    public function testId()
    {
        $node = new Node();

        $this->assertIsInt($node->getId());
    }

    public function testNodeTotal()
    {
        $node = new Node(nodeTotal: 3);

        $this->assertSame(3, $node->nodeTotal());
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
        $this->assertSame('N1', $node->getChildNodeKey('K0'));

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
        $this->assertSame(['K31', 'K4', 'K3', 'K2', 'K1'], $keys);

        $node->insertKey('K10', $this->data, 4);
        $keys = array_keys($node->getKeys());
        $this->assertSame(['K31', 'K10', 'K4', 'K3', 'K2', 'K1'], $keys);

        $node->insertKey('K0', $this->data, 6);
        $keys = array_keys($node->getKeys());
        $this->assertSame(['K31', 'K10', 'K4', 'K3', 'K2', 'K1', 'K0'], $keys);

        $node->insertKey('K20', $this->data);
        $keys = array_keys($node->getKeys());
        $this->assertSame(['K31', 'K20', 'K10', 'K4', 'K3', 'K2', 'K1', 'K0'], $keys);

        $node->insertKey('K', $this->data);
        $keys = array_keys($node->getKeys());
        $this->assertSame(['K31', 'K20', 'K10', 'K4', 'K3', 'K2', 'K1', 'K0', 'K'], $keys);

        $node->insertKey('K', $this->data);
        $keys = array_keys($node->getKeys());
        $this->assertSame(['K31', 'K20', 'K10', 'K4', 'K3', 'K2', 'K1', 'K0', 'K'], $keys);
    }

    public function testSearchKeyPrevTrue()
    {
        $keys = [
            'N<Owen17' => new Node(
                true,
                [
                    'K-Roman44' => new Data(new \DateTime()),
                    'K-Peter31' => new Data(new \DateTime()),
                ],
                2
            ),
            'K-Owen17' => new Data(new \DateTime()),
            'N>Owen17' => new Node(
                true,
                [
                    'K-Olga28' => new Data(new \DateTime()),
                ],
                1
            ),
            'K-Lisa44' => new Data(new \DateTime()),
            'N<Artur28' => new Node(
                true,
                [
                    'K-Lisa34' => new Data(new \DateTime()),
                    'K-Ivan17' => new Data(new \DateTime()),
                ],
                2
            ),
            'K-Artur28' => new Data(new \DateTime()),
            'N>Artur28' => new Node(
                true,
                [
                    'K-Alex31' => new Data(new \DateTime()),
                    'K-Alex21' => new Data(new \DateTime()),
                ],
                2
            ),
        ];
        $node = new Node(isLeaf: false, keys: $keys, keyTotal: 3, nodeTotal: 4);
        $result = $node->searchKeyPrev('K-Owen27', true);

        $this->assertSame(1, count($result));
        $this->assertSame('K-Owen17', array_key_first($result));
        $this->assertInstanceOf(DataInterface::class, array_pop($result));
    }

    public function testSearchKeyPrevFalse()
    {
        $keys = [
            'N<Owen17' => new Node(
                true,
                [
                    'K-Roman44' => new Data(new \DateTime()),
                ],
                2
            ),
            'K-Owen17' => new Data(new \DateTime()),
            'N>Owen17' => new Node(
                true,
                [
                    'K-Ivan17' => new Data(new \DateTime()),
                    'K-Artur28' => new Data(new \DateTime()),
                    'K-Alex31' => new Data(new \DateTime()),
                    'K-Alex21' => new Data(new \DateTime()),
                ],
                4
            ),
        ];
        $node = new Node(isLeaf: false, keys: $keys, keyTotal: 1, nodeTotal: 2);
        $result = $node->searchKeyPrev('K-Owen27', false);

        $this->assertSame(1, count($result));
        $this->assertSame('K-Owen17', array_key_first($result));
        $this->assertInstanceOf(DataInterface::class, array_pop($result));
    }

    public function testExtractFirst()
    {
        $data = new Data(new \DateTime());
        $keys = [
            'K5' => $data,
            'K4' => $this->data,
            'K3' => $this->data,
            'K2' => $this->data,
            'K1' => $this->data,
        ];

        $node = new Node(keys: $keys, keyTotal: 5);
        $first = $node->extractFirst();

        $this->assertSame(array_slice($keys, 0, 1, true), $first);
        $this->assertSame(4, $node->count());


        $node = new Node();
        $first = $node->extractFirst();
        $this->assertSame([], $first);
        $this->assertSame(0, $node->count());
    }

    public function testDropKeyNode()
    {
        $this->assertSame($this->keys['K1'], $this->node->dropKey('K1'));
        $this->assertSame(3, $this->node->nodeTotal());
        $this->assertSame(1, $this->node->count());

        $this->expectError();
        $this->node->dropKey('N1');
    }

    public function testReplaceThreeWithOne()
    {
        $keys = [
            'N3' => new Node(
                true,
                [
                    'K199' => $this->data,
                    'K19' => $this->data
                ],
                2
            ),
            'K18' => $this->data,
            'N2' => new Node(
                true,
                [
                    'K17' => $this->data,
                ],
                2
            ),
            'K13' => $this->data,
            'N1' => new Node(
                true,
                [
                    'K12' => $this->data,
                    'K11' => $this->data
                ],
                2
            ),
        ];

        $node = new Node(false, $keys, 2, 3);
        $newNode = new Node(
            keys: [
                'K17' => $this->data,
                'K16' => $this->data,
                'K15' => $this->data,
                'K14' => $this->data,
            ],
            keyTotal: 4
        );

        $node->replaceThreeWithOne('N2', $newNode, array_flip(array_keys($keys)), true);
        $this->assertSame(1, $node->count());
        $this->assertSame(2, $node->nodeTotal());

        $keys = array_keys($node->getKeys());

        $this->assertSame('N3', $keys[0]);
        $this->assertSame('K18', $keys[1]);
        $this->assertSame('N2', $keys[2]);

        $node = $node->getKeys()[$keys[2]];

        $this->assertSame(['K17', 'K16', 'K15', 'K14'], array_keys($node->getKeys()));



        $keys = [
            'N3' => new Node(
                true,
                [
                    'K199' => $this->data,
                    'K19' => $this->data
                ],
                2
            ),
            'K18' => $this->data,
            'N2' => new Node(
                true,
                [
                    'K17' => $this->data,
                ],
                2
            ),
            'K13' => $this->data,
            'N1' => new Node(
                true,
                [
                    'K12' => $this->data,
                    'K11' => $this->data
                ],
                2
            ),
        ];

        $node = new Node(false, $keys, 2, 3);
        $newNode = new Node(
            keys: [
                'K17' => $this->data,
                'K16' => $this->data,
                'K15' => $this->data,
                'K14' => $this->data,
            ],
            keyTotal: 4
        );

        $node->replaceThreeWithOne('N2', $newNode, array_flip(array_keys($keys)), false);
        $this->assertSame(1, $node->count());
        $this->assertSame(2, $node->nodeTotal());

        $keys = array_keys($node->getKeys());

        $this->assertSame('N2', $keys[0]);
        $this->assertSame('K13', $keys[1]);
        $this->assertSame('N1', $keys[2]);

        $node = $node->getKeys()[$keys[0]];

        $this->assertSame(['K17', 'K16', 'K15', 'K14'], array_keys($node->getKeys()));
    }

    public function testSearchKeyPrev()
    {
        $keys = [
            'N3' => new Node(),
            'K18' => $this->data,
        ];

        $node = new Node(false, $keys, 1, 1);
        $result = $node->searchKeyPrev('K185', true);

        $this->assertArrayHasKey('K18', $result);

        $keys = [
            'K18' => $this->data,
            'N3' => new Node(),
        ];

        $node = new Node(false, $keys, 1, 1);
        $result = $node->searchKeyPrev('K185', true);

        $this->assertArrayHasKey('K18', $result);
    }
}
