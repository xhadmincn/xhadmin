<?php

namespace YcOpen\CloudService;

class Redis
{
    protected $redis;
    public function __construct()
    {
        if (class_exists('\Redis')) {
            $config = include root_path('config') . 'cache.php';
            if (!isset($config['stores']['redis'])) {
                throw new \Exception('请配置config/cache.php redis');
            }
            $redis = new \Redis;
            $redis->connect($config['stores']['redis']['host'], $config['stores']['redis']['port']);
            if ($config['stores']['redis']['password']) {
                $redis->auth($config['stores']['redis']['password']);
            }
            if ($config['stores']['redis']['select']) {
                $redis->select($config['stores']['redis']['select']);
            }
            $this->redis = $redis;
            return;
        }
        throw new \Exception('请安装redis扩展');
    }
    public function __call($name, $arguments)
    {
        return $this->redis->$name(...$arguments);
    }
    public static function __callStatic($name, $arguments)
    {
        $redis = new self;
        return $redis->$name(...$arguments);
    }
}
