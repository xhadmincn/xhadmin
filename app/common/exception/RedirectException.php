<?php

namespace app\common\exception;

use Exception;

class RedirectException extends Exception
{
    protected $redirect;

    /**
     * 错误跳转异常
     * @param string $message
     * @param string $url
     * @param int $code 301无提示调整转，302有提示调整转
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function __construct(string $message, string $url, int $code = 302)
    {
        parent::__construct($message, $code);
        $this->redirect = $url;
    }

    /**
     * 返回跳转地址
     * @return string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function getUrl()
    {
        return $this->redirect;
    }
}