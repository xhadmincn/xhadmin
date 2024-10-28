<?php

namespace loong\oauth\facade;

class Auth
{
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([new \loong\oauth\Auth(), $name], $arguments);
    }
}
