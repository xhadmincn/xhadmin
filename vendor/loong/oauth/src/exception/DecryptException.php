<?php

namespace loong\oauth\exception;

/**
 * EncryptException
 * 解密异常
 * @package loong\oauth\Exception
 */
class DecryptException extends \Exception
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        $message = '解密失败';
        parent::__construct($message, $code, $previous);
    }
}
