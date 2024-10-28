<?php

namespace app\common\utils;

use support\Response;

/**
 * @title JSON返回
 * @desc 控制器描述
 * @author 楚羽幽 <admin@hangpu.net>
 */
/**
 * JSON结果处理类
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
trait Json
{

    /**
     * 返回JSON数据
     * @param mixed $msg
     * @param mixed $code
     * @param mixed $data
     * @return \think\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function json(mixed $msg, mixed $code, mixed $data = [])
    {
        $json['msg'] = $msg;
        $json['code'] = $code;
        $json['data'] = $data;
        return Response::create($json, 'json');
    }
    
    /**
     * 返回成功消息带token
     * @param string $msg
     * @param string $token
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function successToken(string $msg,string $token)
    {
        $data['token'] = $token;
        return self::json($msg, 200, $data);
    }

    /**
     * 返回成功消息
     * @param mixed $msg
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function success(mixed $msg)
    {
        return self::json($msg, 200);
    }

    /**
     * 返回成功消息带数据
     * @param mixed $msg
     * @param mixed $data
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function successFul(mixed $msg, mixed $data)
    {
        return self::json($msg, 200, $data);
    }

    /**
     * 返回成功结果
     * @param mixed $data
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function successRes(mixed $data)
    {
        return self::json('success', 200, $data);
    }

    /**
     * 返回失败消息
     * @param mixed $msg
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function fail(mixed $msg)
    {
        return self::json($msg, 404);
    }

    /**
     * 返回失败待状态码消息
     * @param mixed $msg
     * @param mixed $code
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function failFul(mixed $msg, mixed $code)
    {
        return self::json($msg, $code);
    }

    /**
     * 返回失败消息并重定向
     * @param mixed $msg
     * @param mixed $url
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function failRedirect(mixed $msg,mixed $url)
    {
        return self::json($msg, 302, ['url' => $url]);
    }

    /**
     * 无失败消息直接重定向
     * @param mixed $url
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function redirect($url)
    {
        return self::json('error', 301, ['url' => $url]);
    }
}
