<?php

// +----------------------------------------------------------------------
// | 应用插件中间件配置
// +----------------------------------------------------------------------

return [
    // 根模块中间件
    ''          => [
        \plugin\{TEAM_PLUGIN_NAME}\app\middleware\AuthMiddleware::class,
    ],
    // admin模块中间件
    'admin'     => [
        \plugin\{TEAM_PLUGIN_NAME}\app\admin\middleware\AdminMiddleware::class,
    ],
];
