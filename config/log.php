<?php
$default=[
    'file' => [
        // 日志记录方式
        'type'           => 'File',
        // 日志保存目录
        'path'           => '',
        // 单文件日志写入
        'single'         => false,
        // 独立日志级别
        'apart_level'    => [],
        // 最大日志文件数量
        'max_files'      => 0,
        // 使用JSON格式记录
        'json'           => false,
        // 日志处理
        'processor'      => null,
        // 关闭通道日志写入
        'close'          => false,
        // 日志输出格式化
        'format'         => '[%s][%s] %s',
        // 是否实时写入
        'realtime_write' => false,
    ],
    'swoole' => [
        // 日志记录方式
        'type'           => 'File',
        // 日志保存目录
        'path'           => runtime_path('swoole'),
        // 单文件日志写入
        'single'         => false,
        // 独立日志级别
        'apart_level'    => [],
        // 最大日志文件数量
        'max_files'      => 0,
        // 使用JSON格式记录
        'json'           => false,
        // 日志处理
        'processor'      => null,
        // 关闭通道日志写入
        'close'          => false,
        // 日志输出格式化
        'format'         => '[%s][%s] %s',
        // 是否实时写入
        'realtime_write' => false,
    ],
    // 其它日志通道配置
];
$plugin_log=[];
try {
    $files = glob(root_path() . "plugin/*/config/log.php");
    foreach ($files as $path) {
        $list = include $path;
        if (is_array($list)) {
            $plugin_log = array_merge($plugin_log, $list);
        }
    }
} catch (\Throwable $th) {
    //throw $th;
}
return [
    // 默认日志记录通道
    'default'      => 'file',
    // 日志记录级别
    'level'        => [],
    // 日志类型记录的通道 ['error'=>'email',...]
    'type_channel' => [],
    // 关闭全局日志写入
    'close'        => false,
    // 全局日志处理 支持闭包
    'processor'    => null,

    // 日志通道列表
    'channels'     => array_merge($default,$plugin_log),

];
