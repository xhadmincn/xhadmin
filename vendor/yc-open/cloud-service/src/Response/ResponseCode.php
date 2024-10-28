<?php
namespace YcOpen\CloudService\Response;
class ResponseCode
{
    /**
     * 成功
     */
    const SUCCESS = 200;
    /**
     * 失败
     */
    const FAIL = 404;
    /**
     * 需要登录
     */
    const LOGIN = 600;
    /**
     * 验证码错误
     */
    const CAPTCHA = 300;
    /**
     * 站点信息错误
     */
    const SITE_ERROR = 400;
}