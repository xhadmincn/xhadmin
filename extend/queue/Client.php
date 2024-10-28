<?php

namespace queue;


class Client
{
    public static function send($queue, $data)
    {
        Redis::lpush(strtoupper($queue), json_encode($data));
    }
}
