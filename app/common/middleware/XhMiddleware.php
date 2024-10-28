<?php
declare(strict_types=1);

namespace app\common\middleware;

use Closure;
use think\App;
use think\Request;
use think\Response;

/**
 * 插件中间件业务逻辑
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class XhMiddleware
{
    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;
    
    protected $request;
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $app->request;
    }

    /**
     * 业务处理
     * @param \think\Request $request
     * @param \Closure $next
     * @return \think\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        return $response;
    }

    /**
     * 检测路由是否为插件后台
     * @param string $route
     * @return bool
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected function isAdmin(string $route)
    {
        $route = trim($route, '/');
        $admins = $this->getAdminViewRouteList($this->request->plugin);
        if (in_array($route, $admins)) {
            return true;
        }
        return false;
    }

    /**
     * 获取当前插件后台视图路由
     * @param string $route
     * @return string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected function getAdminViewRoute(string $route)
    {
        $admins = $this->getAdminViewRouteList($this->request->plugin);
        foreach ($admins as $value) {
            if (strpos($route, $value) !== false) {
                return $value;
            }
        }
        return '';
    }

    /**
     * 获取当前插件后台视图路由列表
     * @param mixed $pluginName
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected function getAdminViewRouteList($pluginName)
    {
        $data = config("plugin.{$pluginName}.admin", []);
        foreach ($data as $key => $value) {
            $adminName  = $value ? "/{$value}" : '';
            $data[$key] = "app/{$pluginName}{$adminName}";
        }
        return $data;
    }
}
