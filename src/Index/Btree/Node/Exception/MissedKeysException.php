<?php

namespace Btree\Index\Btree\Node\Exception;

use Exception;

/**
 * Class MissedKeysException
 *
 * @package assassin215k/btree
 */
class MissedKeysException extends Exception
{
    protected $message = "No keys found in the Node";

    public function __construct()
    {
        parent::__construct($this->message);
    }
}
