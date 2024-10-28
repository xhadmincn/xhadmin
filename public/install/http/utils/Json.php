<?php
/**
 * @title JSON返回
 * @desc 控制器描述
 * @author 楚羽幽 <admin@hangpu.net>
 */
class Json
{

    /**
     * 返回JSON数据
     *
     * @param mixed $msg
     * @param mixed $code
     * @param array $data
     * @return string
     */
    public static function json(mixed $msg, mixed $code, array $data = []): string
    {
        $json['msg'] = $msg;
        $json['code'] = $code;
        $json['data'] = $data;
        return json_encode($json,256);
    }

    /**
     * 返回成功消息
     *
     * @param string $msg
     * @return string
     */
    public static function success(string $msg): string
    {
        return self::json($msg, 200);
    }

    /**
     * 返回成功消息带数据
     *
     * @param string $msg
     * @param array $data
     * @return string
     */
    public static function successFul(string $msg, array $data): string
    {
        return self::json($msg, 200, $data);
    }

    /**
     * 返回成功结果
     *
     * @param array $data
     * @return string
     */
    public static function successRes(array $data): string
    {
        return self::json('success', 200, $data);
    }

    /**
     * 返回失败消息
     *
     * @param string $msg
     * @return string
     */
    public static function fail(string $msg): string
    {
        return self::json($msg, 404);
    }

    /**
     * 返回失败待状态码消息
     *
     * @param string $msg
     * @param integer $code
     * @return string
     */
    public static function failFul(string $msg, int $code): string
    {
        return self::json($msg, $code);
    }
}
