<?php

namespace app\common\exception;

use Exception;
use think\facade\Log;

/**
 * 回滚专用异常类
 * @author 贵州猿创科技有限公司
 * @copyright (c) 贵州猿创科技有限公司
 */
class RollBackException extends Exception
{
    /**
     * 构造函数
     * @param string $message
     * @param mixed $code
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function __construct(string $message,mixed $code = 404)
    {
        $content = "{$message}，line：{$this->getLine()}，file：{$this->getFile()}";
        Log::write($content,"xhadmin_rollback");
        parent::__construct($message, $code);
    }
}