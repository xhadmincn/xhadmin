<?php
namespace support;

/**
 * 应用请求对象类
 * @author 贵州猿创科技有限公司
 * @copyright (c) 2023
 */
class Request extends \think\Request
{
    /**
     * 获取当前请求URL
     * @return string
     * @author John
     */
    public function path()
    {
        return $this->pathinfo();
    }
}