<?php

namespace queue;


class Redis
{
    public $handler;
    protected $options = [
        'host'       => '127.0.0.1',
        'port'       => 6379,
        'password'   => '',
        'select'     => 0,
        'timeout'    => 0,
        'expire'     => 0,
        'persistent' => false,
        'prefix'     => '',
    ];
    public function __construct()
    {
        $options = config('cache.stores.redis');
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }


        if (extension_loaded('redis')) {
            $this->handler = new \Redis;

            if ($this->options['persistent']) {
                $this->handler->pconnect($this->options['host'], (int) $this->options['port'], (int) $this->options['timeout'], 'persistent_id_' . $this->options['select']);
            } else {
                $this->handler->connect($this->options['host'], (int) $this->options['port'], (int) $this->options['timeout']);
            }

            if ('' != $this->options['password']) {
                $this->handler->auth($this->options['password']);
            }
        } elseif (class_exists('\Predis\Client')) {
            $params = [];
            foreach ($this->options as $key => $val) {
                if (in_array($key, ['aggregate', 'cluster', 'connections', 'exceptions', 'prefix', 'profile', 'replication', 'parameters'])) {
                    $params[$key] = $val;
                    unset($this->options[$key]);
                }
            }

            if ('' == $this->options['password']) {
                unset($this->options['password']);
            }

            $this->handler = new \Predis\Client($this->options, $params);

            $this->options['prefix'] = '';
        } else {
            throw new \BadFunctionCallException('not support: redis');
        }

        if (0 != $this->options['select']) {
            $this->handler->select((int) $this->options['select']);
        }
    }
    public static function __callStatic($name, $arguments)
    {
        $redis = new self();
        $result=call_user_func_array([$redis->handler, $name], $arguments);
        $redis->handler->close();
        return $result;
    }
}