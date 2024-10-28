<?php
declare(strict_types=1);

namespace plugin\{TEAM_PLUGIN_NAME}\app\admin\middleware;

use Closure;
use plugin\{TEAM_PLUGIN_NAME}\app\model\PluginAdmin;
use support\Request;
use think\facade\Session;

/**
 * 应用后台权限中间件
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-11
 */
class AdminMiddleware
{
    /**
    * 中间件入口
    * @author 贵州猿创科技有限公司
    * @Email 416716328@qq.com
    * @DateTime 2023-03-11
    */
    public function handle(Request $request, Closure $next)
    {
        // 鉴权检测
        try {
            $this->canAccess($request);
            $response = $request->method() == 'OPTIONS' ? response('', 204) : $next($request);
        } catch (\Throwable $th) {
            $response = json(['code' => $th->getCode(), 'msg' => $th->getMessage(), 'file' => $th->getFile()]);
        }
        return $response;
    }

    /**
    * 业务逻辑
    * @author 贵州猿创科技有限公司
    * @Email 416716328@qq.com
    * @DateTime 2023-03-11
    */
    public function canAccess(Request $request): bool
    {
        # 无控制器地址
        if (!$request->control) {
            return true;
        }
        # 获取控制器鉴权信息
        $namespace = app()->getNamespace();
        $classNameSpace = "{$namespace}\\{$request->control}";
        $class = new \ReflectionClass($classNameSpace);
        $properties = $class->getDefaultProperties();
        # 无需登录方法
        $noNeedLogin = $properties['noNeedLogin'] ?? [];
        # 无需鉴权方法
        $noNeedAuth = $properties['noNeedAuth'] ?? [];
        # 无需验证APPID方法
        $noNeedAppid = $properties['noNeedAppid'] ?? [];
        # 设置全局Appid
        $request->appid = $request->header('Appid');
        if (!in_array($request->action(), $noNeedAppid)) {
            if (!$request->appid) {
                throw new \Exception('访问应用不存在', 404);
            }
        }
        # 不需要登录
        if (in_array($request->action(), $noNeedLogin)) {
            return true;
        }
        # 令牌验证
        $admin = Session::get('{TEAM_PLUGIN_NAME}');
        if(!$admin){
            throw new \Exception('登录已过期，请重新登录', 12000);
        }
        $request->user                  = $admin;
        $request->userId                = $admin['id'];
        $request->saas_appid            = $admin['saas_appid'];

        # 不需要鉴权
        if (in_array($request->action(), $noNeedAuth)) {
            return true;
        }
        # 系统级部门，不需要鉴权
        if ($admin['role']['is_system'] == '20') {
            return true;
        }
        # 获取管理员角色规则
        $rule = PluginAdmin::getAdminRule($admin['id']);
        # 检测是否有操作权限
        $ctrlName = str_replace('Controller', '', basename(str_replace('\\', '/', $request->control)));
        $path = "{$ctrlName}/{$request->actionName}";
        if (!in_array($path, $rule)) {
            throw new \Exception("没有该操作权限", 10001);
        }
        return true;
    }
}
