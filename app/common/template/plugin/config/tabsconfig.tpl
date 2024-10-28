<?php

/** 
 * 
 * 选项卡配置项（active）代表默认选中
 * 方法名：tabs
 * 
 * 储存数据格式：
 * {
 * "active":"xxx",
 * "children":{
 *      "xxxx":{
 *          "xxx":"xxxx",
 *          "xxxx":"xxx"
 *      },
 *    }
 * }
 * 
 * 
 * 附件库配置项
 * 方法名：uploadify
 * 
 * 储存数据格式：
 * {
 * "active":"xxx",
 * "children":{
 *      "xxxx":{
 *          "xxx":"xxxx",
 *          "xxxx":"xxx"
 *      },
 *    }
 * }
 */

$data = [];
foreach (glob(__DIR__ . '/tabs/*.php') as $path) {
    $content = file_get_contents($path);
    if (empty($content)) {
        continue;
    }
    $group        = basename($path, '.php');
    $data[$group] = require $path;
}
return $data;
