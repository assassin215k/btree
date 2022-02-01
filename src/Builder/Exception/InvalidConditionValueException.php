<?php

namespace Btree\Builder\Exception;

use Exception;

/**
 * Class InvalidConditionValueException
 *
 * @package assassin215k/btree
 */
class InvalidConditionValueException extends Exception
{
    protected $message = "Invalid value(s) for condition operation!";

    public function __construct()
    {
        parent::__construct($this->message);
    }
}
