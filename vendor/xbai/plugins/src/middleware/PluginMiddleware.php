<?php
declare(strict_types=1);

namespace Xbai\Plugins\middleware;

use Closure;
use think\App;
use think\exception\HttpException;
use think\Request;
use think\Response;
use Xbai\Plugins\utils\PluginsUtil;

/**
 * 插件业务处理
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class PluginMiddleware
{

    /** @var App */
    protected $app;

    /**
     * 插件工具类
     * @var PluginsUtil
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $pluginUtil;

    /**
     * 中间件构造函数
     * @param \think\App $app
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 多应用插件配置
     * @access public
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        // 检测是否资源文件
        if ($response = getAssetsCheck($request)) {
            return $response;
        }
        // 实例插件工具
        $pluginUtil       = new PluginsUtil($this->app);
        $this->pluginUtil = $pluginUtil;
        // 1.初始化应用插件基础参数
        $this->pluginUtil->initPlugin();
        // 2.解析路由
        $this->parseRoute();
        // 3.加载插件配置
        $this->pluginUtil->loadConfig();
        // 4.加载应用插件composer包
        $this->pluginUtil->loadComposer();
        // 5.注册插件中间件
        $this->registerMiddlewares();

        // 调度转发
        return $this->app->middleware
            ->pipeline('plugin')
            ->send($request)
            ->then(function ($request) use ($next) {
                return $next($request);
            });
    }

    /**
     * 解析路由
     * @throws \Exception
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private function parseRoute()
    {
        $plugin   = $this->app->request->route('plugin', '');
        $appid   = $this->app->request->route('appid', '');
        $pathinfo = $this->app->request->pathinfo();
        // 静态资源则拦截
        if ($response = getAssetsCheck($this->app->request)) {
            echo '2';die;
            print_r($response);
            exit;
            return $response;
        }
        if (empty($plugin)) {
            throw new \Exception("插件名称不能为空");
        }
        // 设置插件名称
        $this->app->request->plugin = $plugin;
        $this->app->request->appid = $this->app->request->header('appid');
        if($appid){
            # 设置Appid
            $header=$this->app->request->header();
            $this->app->request->withHeader(array_merge($header, [
                'Appid'=>(string)$appid
            ]));
            $this->app->request->appid = $appid;
        }
        // 解析路由
        $pathinfo  = str_replace("app/{$plugin}", '', $pathinfo);
        $routeinfo = trim($pathinfo, '/');
        $pathArr   = explode('/', $routeinfo);
        $pathCount = count($pathArr);
        // 取控制器
        $control = config('route.default_controller', 'Index');
        // 取方法名
        $action = config('route.default_action', 'index');
        if ($pathCount > 1 && !is_dir($this->app->getRootPath() . "plugin/{$plugin}/app/" . $routeinfo)) {
            // 控制器
            $controlIndex = $pathCount - 2;
            $control      = ucfirst($pathArr[$controlIndex]);
            unset($pathArr[$pathCount - 2]);
        }
        if ($pathCount > 1) {
            // 方法
            $acionIndex = $pathCount - 1;
            $action     = $pathArr[$acionIndex];
            unset($pathArr[$pathCount - 1]);
        }
        $isControlSuffix             = config('route.controller_suffix', true);
        $controllerSuffix            = $isControlSuffix ? 'Controller' : '';
        $this->app->request->control = "{$control}{$controllerSuffix}";
        $this->app->request->action  = $action;
        $this->app->request->setController($control);
        $this->app->request->setAction($action);

        // 层级
        $this->app->request->levelRoute = implode('/', $pathArr);
        // 命名空间
        $levelRoute = '';
        if ($this->app->request->levelRoute) {
            $levelRoute = str_replace("/", "\\", $this->app->request->levelRoute);
            $levelRoute = "{$levelRoute}\\";
        }
        $controlLayout = config('route.controller_layer', 'controller');
        $this->app->setNamespace("plugin\\{$plugin}\\app\\{$levelRoute}{$controlLayout}");
    }

    /**
     * 注册插件中间件
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private function registerMiddlewares()
    {
        $request = $this->app->request;
        // 获取框架全局中间件
        $middleware = config('plugins.middleware', []);
        // 获取层级中间件
        $levelMiddleware = $this->app->request->levelRoute;
        // 获取插件应用中间件
        $pluginMiddleware = config("plugin.{$request->plugin}.middleware.{$levelMiddleware}", []);
        // 合并中间件
        $middlewares = array_merge($middleware, $pluginMiddleware);
        // 注册插件全局中间件
        $this->app->middleware->import($middlewares, 'plugin');
    }
}