<?php

return [
    // 默认磁盘
    'default' => 'local',
    // 磁盘列表
    'disks' => [
        'local' => [
            'type' => 'local',
            'root' => app()->getRuntimePath() . 'storage',
        ],
        // 更多的磁盘配置信息
        'aliyun' => [
            'type' => 'aliyun',
            'access_id' => '',
            'access_secret' => '',
            'bucket' => '',
            'endpoint' => '',
            'isCName' => true,
        ],
        'qiniu' => [
            'type' => 'qiniu',
            'access_key' => '******',
            'secret_key' => '******',
            'bucket' => '',
            'domain' => '',
        ],
        'qcloud' => [
            'type' => 'qcloud',
            'region' => '***',
            //bucket 所属区域 英文
            'app_id' => '***',
            // 域名中数字部分
            'secret_id' => '***',
            'secret_key' => '***',
            'bucket' => '***',
            'timeout' => 60,
            'connect_timeout' => 60,
        ]
    ],
];