<?php

namespace app\common\utils;

use think\Container;
use think\swoole\Websocket;

/**
 * swoole配置工具类
 * @author 贵州猿创科技有限公司
 * @copyright (c) 贵州猿创科技有限公司
 */
class SwooleUtil
{
    private function getConfig()
    {
        $data = [];
        # 扫描配置文件
        $websocket = glob(root_path().'plugin/*/config/socketEvent.php');
        if (empty($websocket)) {
            return $data;
        }
        foreach ($websocket as $path) {
            $config = require $path;
            if (empty($config) || !is_array($config)) {
                continue;
            }
            foreach ($config as $class) {
                if (!class_exists($class)) {
                    continue;
                }
                $data[] = $class;
            }
        }
        # 去除空值
        $data = array_filter($data);
        # 去除重复
        $data = array_unique($data);
        # 返回数据
        return $data;
    }
    public static function getData()
    {
        $data=[];
        # 扫描配置文件
        $task = glob(root_path().'plugin/*/config/task.php');
        if (empty($task)) {
            return $data;
        }
        foreach ($task as $path) {
            $config = require $path;
            if (empty($config) || !is_array($config)) {
                continue;
            }
            # 插件名称
            $plugin = basename(dirname(dirname($path)));
            $packagePath = root_path("plugin/{$plugin}/package");
            $package='';
            if (is_dir($packagePath) && file_exists($packagePath . "/vendor/autoload.php")) {
                $package=$packagePath . "/vendor/autoload.php";
            }
            foreach ($config as $key => $value) {
                $value['plugin']=$plugin;
                $value['package']=$package;
                $value['queue_name'] = $plugin.$key;
                $data[] = $value;
            }
        }
        # 返回数据
        return $data;
    }

    /**
     * 获取应用的队列
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function getQueue()
    {
        $queue = [];
        $list=self::getData();
        foreach ($list as $key => $value) {
            if (isset($value['type']) && $value['type'] == 'queue') {
                $queue[] = $value;
            }
        }
        # 返回数据
        return $queue;
    }
    public static function getTask()
    {
        $task = [];
        $list=self::getData();
        foreach ($list as $key => $value) {
            if (!(isset($value['type']) && $value['type'] == 'queue')) {
                $task[] = $value;
            }
        }
        # 返回数据
        return $task;
    }
}
