<?php

namespace Btree\Exception;

use Exception;

/**
 * Class IndexExistException
 *
 * @package assassin215k/btree
 */
class IndexDuplicationException extends Exception
{
    protected $message = "Index '%1s' already exist";

    /**
     * @param string $indexKey
     */
    public function __construct(string $indexKey)
    {
        parent::__construct(sprintf($this->message, $indexKey));
    }
}
