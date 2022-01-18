<?php

namespace Btree\Index;

use Btree\Node\Node;
use Btree\Node\NodeInterface;

/**
 * Class Index
 *
 * Index to contain index cache
 *
 * @package assassin215k/btree
 */
class BtreeIndex implements IndexInterface
{
    public static int $nodeSize = 16;

    private Node $root;
    private readonly array|string $fields;

    public function __construct(array|string $fields)
    {
        self::$nodeSize = 2;

        $this->fields = is_array($fields) ? $fields : [$fields];
        $this->root = new Node();
    }

    public function insert(object $value): void
    {
        $hash = $this->encode($value->getName().$value->getAge());

//        $this->root-> ($hash, $value);
//        var_dump($hash);
//        if ($this->root)
    }

    public function search(string $value): array
    {
        return [];
    }

//    private function findNode():NodeInterface {
//
//    }

    function encode($string) {
        $ans = array();
//        $string = str_split($string);
        #go through every character, changing it to its ASCII value

        $length = strlen($string);
        for ($i=0; $i<$length; $i++) {
            $ascii = ord($string[$i]);
            $ans[] = $ascii;
        }
        unset($ascii);

//        for ($i = 0; $i < count($string); $i++) {
//
//            #ord turns a character into its ASCII values
//            $ascii = (string) ord($string[$i]);
//
//            #make sure it's 3 characters long
//            if (strlen($ascii) < 3)
//                $ascii = '0'.$ascii;
//            $ans[] = $ascii;
//        }

        #turn it into a string
        return implode('', $ans);
    }

    function decode($string) {
        $ans = '';
        $string = str_split($string);
        $chars = array();

        #construct the characters by going over the three numbers
        for ($i = 0; $i < count($string); $i+=3)
            $chars[] = $string[$i] . $string[$i+1] . $string[$i+2];

        #chr turns a single integer into its ASCII value
        for ($i = 0; $i < count($chars); $i++)
            $ans .= chr($chars[$i]);

        return $ans;
    }
}
