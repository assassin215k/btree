<?php

namespace Btree\Exception;

use Exception;

/**
 * Class MissedPropertyException
 *
 * @package assassin215k/btree
 */
class MissedPropertyException extends Exception
{
    protected $message = "Property '%1s' is missed in the object '%2s'!";

    /**
     * @param string $property
     * @param object $object
     */
    public function __construct(string $property, object $object)
    {
        parent::__construct(sprintf($this->message, $property, get_class($object)));
    }
}
