<?php
$cpunum=swoole_cpu_num();
return [
    'coroutine' => [
        'enable' => true,
        'flags' => SWOOLE_HOOK_ALL|SWOOLE_HOOK_CURL,
    ],
    'http' => [
        'enable' => true,
        'host' => '0.0.0.0',
        'port' => env('SWOOLE_HTTP_SERVER_PORT', 39600),
        // 'worker_num' => swoole_cpu_num(),
        'worker_num' => 1,
        'options' => [],
    ],
    'websocket' => [
        'enable' => true,
        'handler' => \think\swoole\websocket\Handler::class,
        'ping_interval' => 25000,
        'ping_timeout' => 60000,
        'room' => [
            'type' => 'table',
            'table' => [
                'room_rows' => 8192,
                'room_size' => 2048,
                'client_rows' => 4096,
                'client_size' => 2048,
            ],
            'redis' => [
                'host' => env('REDIS_REDIS_HOST', '127.0.0.1'),
                'port' => env('REDIS_REDIS_PORT', 6379),
                'max_active' => 3,
                'max_wait_time' => 5,
            ],
        ],
        'listen'        => [
            'open'      => \app\common\event\WebSocketOpen::class,
            'message'   => \app\common\event\WebSocketMessage::class,
            'event'     => \app\common\event\WebSocketEvent::class,
            'close'     => \app\common\event\WebSocketClose::class,
        ],
        'subscribe'     => [],
    ],
    'rpc' => [
        'server' => [
            'enable' => false,
            'host' => '0.0.0.0',
            'port' => 9000,
            // 'worker_num' => swoole_cpu_num(),
            'worker_num' => 1,
            'services' => [],
        ],
        'client' => [],
    ],
    //队列
    'queue' => [
        'enable' => false,
        'workers' => [
            //下面参数是不设置时的默认配置
            'default' => [
                'delay' => 0,
                'sleep' => 3,
                'tries' => 0,
                'timeout' => 60,
                'worker_num' => 1,
            ],
            //使用@符号后面可指定队列使用驱动
            'default@connection' => [
                // 此处可不设置任何参数，使用上面的默认配置
                'delay' => 0,
                'sleep' => 3,
                'tries' => 0,
                'timeout' => 60,
                'worker_num' => 1,
            ]
        ]
    ],
    'hot_update' => [
        'enable' => env('APP_DEBUG', true),
        'name' => ['*.php'],
        'include' => [
            app_path(),
            root_path() . 'plugin/',
        ],
        'exclude' => [],
    ],
    //连接池
    'pool' => [
        'db' => [
            'enable' => true,
            'max_active' => $cpunum*8,
            'max_wait_time' => 5,
        ],
        'cache' => [
            'enable' => true,
            'max_active' => $cpunum*8,
            'max_wait_time' => 5,
        ],
        //自定义连接池
    ],
    'ipc' => [
        'type' => 'unix_socket',
        'redis' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'max_active' => 3,
            'max_wait_time' => 5,
        ],
    ],
    'tables' => [],
    //每个worker里需要预加载以共用的实例
    'concretes' => [],
    //重置器
    'resetters' => [],
    //每次请求前需要清空的实例
    'instances' => [],
    //每次请求前需要重新执行的服务
    'services' => [],
];
