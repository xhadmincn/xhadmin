<?php

namespace loong\oauth\exception;

/**
 * LockException
 * Class LockException
 * @package loong\oauth\Exception
 */
class LockException extends \Exception
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
