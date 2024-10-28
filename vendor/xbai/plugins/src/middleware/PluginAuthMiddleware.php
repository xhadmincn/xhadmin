<?php

declare(strict_types=1);

namespace Xbai\Plugins\middleware;

use Closure;
use think\App;
use think\exception\HttpException;
use think\Request;
use think\Response;
use think\facade\Session;
use app\common\model\Store;
use app\common\model\StoreApp;
use app\common\model\StorePlugins;
use app\common\model\StorePluginsExpire;

class PluginAuthMiddleware
{

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
        $appid = $request->appid;
        $auth_store_state = Session::get('auth_store_state.' . $appid);
        if ($appid && !$auth_store_state) {
            $StoreApp = StoreApp::where(['id' => $appid])->find();
            if (!$StoreApp) {
                return $this->fail($request, '应用不存在');
            }
            $store = Store::where(['id' => $StoreApp['store_id']])->find();
            if (!$store) {
                return $this->fail($request, '商户不存在');
            }
            if (!$store->plugins_name) {
                $StorePluginsExpire = StorePluginsExpire::where(['id' => $StoreApp->auth_id])->find();
                if (!$StorePluginsExpire) {
                    return $this->fail($request, '插件未授权');
                }
                if ($StorePluginsExpire->expire_time < date('Y-m-d')) {
                    return $this->fail($request, '插件已过期，请联系管理员');
                }
            }
            Session::set('auth_store_state.' . $appid, 1);
        }
        // 调度转发
        return $next($request);
    }
    public function fail($request, $msg)
    {
        $msg = "插件授权异常：{$msg}";
        if ($request->isJson()) {
            return Response::create(['code' => 404, 'msg' => $msg], 'json', 200);
        }
        return Response::create($msg, 'html', 404);
    }
}
