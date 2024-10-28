<?php
namespace app;

use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Response;
use Throwable;
use app\common\exception\RedirectException;

/**
 * 全局异常处理类
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 添加自定义异常处理机制
        if ($request->isJson()) {
            $code = $e->getCode();
            $json = [
                'code'          => $code ?: 500,
                'msg'           => $e->getMessage(),
            ];
            # 跳转异常处理
            if ($e instanceof RedirectException) {
                $json['data'] = [
                    'url' => $e->getUrl(),
                ];
            }
            // 调试环境错误
            if (env('APP_DEBUG',false)) {
                $json['trace']['line'] = $e->getLine();
                $json['trace']['file'] = $e->getFile();
                $json['trace']['trace'] = $e->getTrace();
            }
            $json = json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            return response($json, 200, ['Content-Type' => 'application/json']);
        }
        // 其他错误交给系统处理
        return parent::render($request, $e);
    }
}
