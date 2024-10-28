<?php

namespace app\common\exception;

use Exception;

class ErrorException extends Exception
{
    protected $redirect;

    /**
     * 错误跳转异常
     * @param string $message
     * @param mixed $code
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function __construct(string $message,mixed $code = 404)
    {
        parent::__construct($message, $code);
    }
}