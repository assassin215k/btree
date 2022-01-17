<?php

namespace Btree\Exception;

use Exception;

/**
 * Class IndexMissingException
 *
 * @package assassin215k/btree
 */
class IndexMissingException extends Exception
{
    protected $message = "Index '%1s' is missed";

    /**
     * @param string $indexKey
     */
    public function __construct(string $indexKey)
    {
        parent::__construct(sprintf($this->message, $indexKey));
    }
}
