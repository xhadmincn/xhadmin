<?php

namespace loong\oauth\exception;

/**
 * EncryptException
 * 加密异常
 * @package loong\oauth\Exception
 */
class EncryptException extends \Exception
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        $message = '加密失败';
        parent::__construct($message, $code, $previous);
    }
}
