<?php

namespace Btree\Exception;

use Exception;

/**
 * Class InvalidIndexClassException
 *
 * @package assassin215k/btree
 */
class InvalidIndexClassException extends Exception
{
    protected $message = "Specified invalid class";

    /**
     * @param string $indexKey
     */
    public function __construct()
    {
        parent::__construct($this->message);
    }
}
