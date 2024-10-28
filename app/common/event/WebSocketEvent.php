<?php

namespace app\common\event;

use think\Container;
use think\swoole\Websocket;
use think\swoole\websocket\Event;

class WebSocketEvent
{
    use EventTrait;
    
    /**
     * Websocket
     * @var Websocket
     * 
     */
    protected $websocket = null;
    protected $container = null;
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->websocket = $container->make(Websocket::class);
    }

    /**
     * 消息事件
     * @param mixed $event
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function handle(Event $event)
    {
        try {
            # 获取配置项
            $config = $this->getConfig($event,'event');
            $data = $event->data;
            # 执行类
            $class = new $config['className']($this->container);
            # 执行调度转发
            $data = call_user_func_array([$class,'handle'],[$event,$data]);
        } catch (\Throwable $e) {
            return $this->websocket->emit('fail', [
                'code'  => $e->getCode(),
                'msg'   => $e->getMessage(),
            ]);
        }
        return $this->websocket->emit('success',[]);
    }
}
