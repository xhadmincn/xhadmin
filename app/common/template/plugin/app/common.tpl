<?php

# 插件函数库文件

/**
* 获取单页内容
* @return void
* @author 贵州猿创科技有限公司
* @copyright 贵州猿创科技有限公司
* @email 416716328@qq.com
*/
function getPageDetail(string $name)
{
    $model = new \plugin\{TEAM_PLUGIN_NAME}\app\model\{PLUGIN_NAME}OnePage;
    $where = [
        'name'      => $name
    ];
    $model = $model->where($where)->find();
    return $model;
}