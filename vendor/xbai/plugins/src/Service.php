<?php
namespace Xbai\Plugins;

use support\Request;
use think\Route;
use think\Service as BaseService;
use Xbai\Plugins\middleware\PluginMiddleware;
use Xbai\Plugins\middleware\PluginAuthMiddleware;

/**
 * 插件服务
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class Service extends BaseService
{
    /**
     * 引导服务
     * @param \think\Route $route
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function boot(Route $route)
    {
        // 检测插件目录不存在则创建
        if (!is_dir($this->app->getRootPath() . 'plugin')) {
            mkdir($this->app->getRootPath() . 'plugin', 0755, true);
        }
        $execute = '\\Xbai\\Plugins\\service\\RouteService@execute';
        // 使用composer注册插件命名空间
        $this->registerNamespace();
        # 域名映射插件
        $baseUrl=$this->app->request->baseUrl();
        if($baseUrl=='/'){
            $pluginsDomains=config('yc_plugins_domains');
            $host=$this->app->request->host();
            if(!empty($pluginsDomains)&&isset($pluginsDomains[$host])){
                $plugin=$pluginsDomains[$host];
                $route->rule("/", $execute)
                ->append(['plugin'=>$plugin['plugin'],'appid'=>$plugin['appid']])
                ->middleware([PluginMiddleware::class,PluginAuthMiddleware::class]);
            }
        # baseUrl是以/app/开头的
        }elseif(strpos($baseUrl,'/app/')===0){
            // 监听服务
            $this->app->event->listen('HttpRun', function () use ($route,$execute) {
                // 注册插件路由
                $route->rule("app/:plugin", $execute)
                ->middleware([PluginMiddleware::class,PluginAuthMiddleware::class]);
            });
        }
    }
    /**
     * 使用composer注册插件命名空间
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private function registerNamespace()
    {
        // 扫描插件目录并排除.和..
        $data = array_diff(scandir($this->app->getRootPath() . 'plugin'), ['.', '..']);
        // 实例命名空间类
        $loader = require $this->app->getRootPath() . 'vendor/autoload.php';
        // 绑定服务
        $this->app->bind('rootLoader', $loader);
        // 注册命名空间
        foreach ($data as $pluginName) {
            if (is_dir($this->app->getRootPath() . 'plugin/' . $pluginName)) {
                $pluginPath = $this->app->getRootPath() . "plugin/{$pluginName}/";
                $loader->setPsr4("plugin\\{$pluginName}\\", $pluginPath);
            }
        }
    }
}