<?php
namespace support;

/**
 * Redis类
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class Redis
{
    /**
     * 静态调用
     * @param mixed $name
     * @param mixed $arguments
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function __callStatic($name, $arguments)
    {
        $config = config('cache.stores.redis',[]);
        $redis = new \think\cache\driver\Redis($config);
        $redis = $redis->handler();
        return $redis->$name(...$arguments);
    }
}