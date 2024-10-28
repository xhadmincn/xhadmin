<?php

namespace app\common\event;

use think\Container;
use think\swoole\Websocket;

class WebSocketMessage
{
    use EventTrait;
    
    /**
     * websocket
     * @var Websocket
     */
    protected $websocket = null;

    /**
     * 构造函数
     * @param \think\Container $container
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function __construct(Container $container)
    {
        $this->websocket = $container->make(Websocket::class);
    }

    public function handle($event)
    {
    }
}
