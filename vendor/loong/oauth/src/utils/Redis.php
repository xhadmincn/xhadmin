<?php

namespace loong\oauth\utils;

class Redis
{
    protected $redis;
    public function __construct()
    {
        if (class_exists('\Redis')) {
            $oauthFile = config_path() . '/oauth.php';
            if (file_exists($oauthFile)) {
                $config = include $oauthFile;
            } else {
                $config = include config_path() . '/cache.php';
                if (!isset($config['stores']['redis'])) {
                    throw new \Exception('请配置config/cache.php redis');
                }
                $config = $config['stores'];
            }
            $redis = new \Redis;
            $redis->connect($config['redis']['host'], $config['redis']['port']);
            if ($config['redis']['password']) {
                $redis->auth($config['redis']['password']);
            }
            if ($config['redis']['select']) {
                $redis->select($config['redis']['select']);
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
