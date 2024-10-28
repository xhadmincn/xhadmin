<?php

namespace loong\oauth\exception;

/**
 * SingleException
 * 单点登录异常
 * @package loong\oauth\Exception
 */
class SingleException extends \Exception
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        $message = '已在其他地方登录';
        parent::__construct($message, $code, $previous);
    }
}
