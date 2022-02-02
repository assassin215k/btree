<?php

namespace Btree\Builder\Exception;

use Exception;

/**
 * Class MissedFieldValueException
 *
 * @package assassin215k/btree
 */
class MissedFieldValueException extends Exception
{
    protected $message = "Condition is required a value!";

    public function __construct()
    {
        parent::__construct($this->message);
    }
}
