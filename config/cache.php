<?php

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 默认缓存驱动
    'default' => env('REDIS_REDIS_DRIVE', 'file'),

    // 缓存连接方式配置
    'stores' => [
        'file' => [
            // 驱动方式
            'type' => 'File',
            // 缓存保存目录
            'path' => '',
            // 缓存前缀
            'prefix' => '',
            // 缓存有效期 0表示永久缓存
            'expire' => 0,
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize' => ['json_encode', 'json_decode'],
        ],
        // 更多的缓存连接
        'redis' => [
            // 驱动方式
            'type' => 'Redis',
            'host' => env('REDIS_REDIS_HOST', '127.0.0.1'),
            'port' => env('REDIS_REDIS_PORT', '6379'),
            'password' => env('REDIS_REDIS_PASSWORD', ''),
            'select' => env('REDIS_REDIS_DB', '0'),
            // 缓存前缀
            'prefix' => env('REDIS_REDIS_PREFIX', 'yc_'),
            // 缓存有效期 0表示永久缓存
            'expire' => 0,
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize' => ['json_encode', 'json_decode'],
        ],
    ],
];