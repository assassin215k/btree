<?php

namespace Btree\Exception;

use Btree\Index\IndexInterface;
use Exception;

/**
 * Class WrongClassException
 *
 * @package assassin215k/btree
 */
class WrongClassException extends Exception
{
    protected $message = "Missed class interface '%1s' for class '%2s'";

    /**
     * @param string $algorithmClass
     */
    public function __construct(string $algorithmClass)
    {
        parent::__construct(sprintf($this->message, IndexInterface::class, $algorithmClass));
    }
}
