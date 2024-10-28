<?php

namespace app\common\event;

use app\common\manager\StoreAppMgr;
use think\swoole\websocket\Event;
use think\swoole\Websocket;
use Exception;

trait EventTrait
{
    /**
     * socket
     * @var Websocket
     */
    protected $websocket;

    /**
     * 获取配置项
     * @param mixed $event
     * @param string $eventName
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    protected function getConfig(Event $event,string $eventName)
    {
        if (empty($event->type)) {
            throw new Exception('缺少请求方法',10001);
        }
        if (!isset($event->data)) {
            throw new Exception('缺少请求数据',10002);
        }
        $data = $event->data;
        if (empty($data['appid'])) {
            throw new Exception('缺少应用ID',10003);
        }
        try {
            $where  = [
                'id'        => $data['appid']
            ];
            $detail = StoreAppMgr::detail($where);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(),11000);
        }
        if (empty($detail)) {
            throw new Exception('该应用不存在',10005);
        }
        if (empty($detail['name'])) {
            throw new Exception('项目应用标识错误',10006);
        }
        $name = $detail['name'];
        $pluginPath = root_path()."/plugin/{$name}/config/webSocketEvent.php";
        if (!file_exists($pluginPath)) {
            throw new Exception('该项目下不存在事件文件',10007);
        }
        $config = require $pluginPath;
        if (!isset($config[$eventName]) || !method_exists($config[$eventName],'handle')) {
            throw new Exception('不存在该事件',10008);
        }
        $class = $config[$eventName];
        $data = [
            'pluginPath'        => $pluginPath,
            'className'         => $class,
            'data'              => $data
        ];
        return $data;
    }
}
