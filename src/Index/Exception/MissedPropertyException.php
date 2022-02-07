<?php

namespace Btree\Index\Exception;

use Exception;

/**
 * Class MissedPropertyException
 *
 * @package assassin215k/btree
 */
class MissedPropertyException extends Exception
{
    protected $message = "Property '%1s' is missed in the object or class '%2s'!";

    /**
     * @param string $property
     * @param object $object
     */
    public function __construct(string $property, object | array $item)
    {
        parent::__construct(sprintf($this->message, $property, is_array($item) ? serialize($item) : get_class($item)));
    }
}
