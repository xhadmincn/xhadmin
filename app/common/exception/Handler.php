<?php

namespace app\common\exception;

use Throwable;
use Webman\Exception\ExceptionHandler;
use Webman\Http\Request;
use Webman\Http\Response;

/**
 * 异常处理
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-22
 */
class Handler extends ExceptionHandler
{
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function render(Request $request, Throwable $exception): Response
    {
        if ($request->expectsJson()) {
            $code = $exception->getCode();
            $json = [
                'code' => $code ?: 500,
                'msg'  => $exception->getMessage(),
            ];
            # 跳转异常处理
            if ($exception instanceof RedirectException) {
                $json['data'] = [
                    'url' => $exception->getUrl(),
                ];
            }
            // 调试环境错误
            if ($this->debug) {
                $json['trace']['line'] = $exception->getLine();
                $json['trace']['file'] = $exception->getFile();
            }
            return new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );
        }
        # 模板字符串捕获处理
        if ($exception instanceof ErrorException) {
            $content = file_get_contents(public_path('/404.html'));
            $error   = "<center>{$exception->getMessage()}</center>";
            $content = str_replace('<center>KFAdmin</center>', $error, $content);
            return new Response(200, [], $content);
        }
        return new Response(200, [], $exception->getMessage());
    }
}