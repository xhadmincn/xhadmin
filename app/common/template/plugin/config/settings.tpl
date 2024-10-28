<?php

/**
 * 系统设置管理
 * settings 普通配置项
 * 储存数据格式：{"xxxx":"xxx","xxxx":"xxx"}
 * 
 * 
 * config 无默认选中选项卡
 * 储存数据格式：{"xxxx":"xxx","xxxx":"xxx"}
 * 
 * 
 * divider 虚线配置项
 * 储存数据格式：{"xxxx":"xxx","xxxx":"xxx"}
 */
$data = [];
foreach (glob(__DIR__ . '/settings/*.php') as $path) {
    $content = file_get_contents($path);
    if (empty($content)) {
        continue;
    }
    $group        = basename($path, '.php');
    $data[$group] = require $path;
}
return $data;
