<?php

declare(strict_types=1);

namespace Xbai\Plugins\service;

use support\Request;
use think\App;

/**
 * 插件业务处理
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class RouteService
{

    /** @var App */
    protected $app;

    /**
     * 请求对象
     * @var Request
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $request;

    /**
     * 构造函数
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function __construct()
    {
        $this->app     = app();
        $this->request = $this->app->request;
    }

    /**
     * 路由注册
     * @param mixed $plugin
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function execute($plugin)
    {
        // 获取三层数据
        $control = $this->request->control;
        $action  = $this->request->action;

        // 组装命名空间
        $pluginNameSpace = "plugin\\{$plugin}";
        $this->app->setNamespace($pluginNameSpace);
        if (!is_dir($this->app->getRootPath() . 'plugin/' . $plugin)) {
            throw new \Exception("插件不存在：{$plugin}");
        }

        // 层级路由
        $levelRoute = '';
        if ($this->request->levelRoute) {
            $levelRoute = str_replace("/", "\\", $this->request->levelRoute);
            $levelRoute = "{$levelRoute}\\";
        }

        // 组装控制器命名空间
        $controlLayout = config('route.controller_layer', 'controller');
        $class         = "{$pluginNameSpace}\\app\\{$levelRoute}{$controlLayout}\\{$control}";
        if (!class_exists($class)) {
            throw new \Exception("插件控制器不存在：{$class}");
        }
        if (!method_exists($class, $action)) {
            throw new \Exception("插件方法不存在：{$class}@{$action}");
        }

        // 执行调度转发
        return app($class)->$action($this->request);
    }
}
