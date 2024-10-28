<?php

namespace loong\ApiStore\facade;

class Http
{
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([new \loong\ApiStore\Http(), $name], $arguments);
    }
}
