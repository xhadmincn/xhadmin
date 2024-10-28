<?php
// 事件定义文件
return [
    'bind'      => [
    ],

    'listen'    => [
        'AppInit'  => [],
        'HttpRun'  => [],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
        'swoole.websocket.Open'     => [
        ],
        // 'swoole.websocket.Message'  => [
        //     \app\common\event\WebSocketMessage::class
        // ],
        'swoole.websocket.Event'    => [
            \app\common\event\WebSocketEvent::class
        ],
        // 'swoole.websocket.Close'    => [
        //     \app\common\event\WebSocketClose::class
        // ],
        'swoole.init'        => [
            \app\common\event\CreateTask::class,
            \app\common\event\CreateQueue::class
        ],
    ],

    'subscribe' => [
    ],
];
