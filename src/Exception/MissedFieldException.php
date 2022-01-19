<?php

namespace Btree\Exception;

use Exception;

/**
 * Class MissedFieldException
 *
 * @package assassin215k/btree
 */
class MissedFieldException extends Exception
{
    protected $message = "Field for index is not specified or empty!";

    public function __construct()
    {
        parent::__construct($this->message);
    }
}
