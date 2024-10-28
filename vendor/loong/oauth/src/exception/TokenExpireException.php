<?php

namespace loong\oauth\exception;

/**
 * TokenExpireException
 * token过期异常
 * @package loong\oauth\Exception
 */
class TokenExpireException extends \Exception
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        $message = 'token已过期';
        parent::__construct($message, $code, $previous);
    }
}
