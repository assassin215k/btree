<?php

namespace Btree\Builder\Exception;

use Exception;

/**
 * Class EmptyFieldException
 *
 * @package assassin215k/btree
 */
class EmptyFieldException extends Exception
{
    protected $message = "Condition field name is not specified";

    public function __construct()
    {
        parent::__construct($this->message);
    }
}
