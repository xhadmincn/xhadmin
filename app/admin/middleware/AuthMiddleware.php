<?php

namespace app\admin\middleware;

use app\common\manager\AuthMgr;
use app\common\model\SystemAdminLog;
use Closure;
use Exception;
use loong\oauth\facade\Auth;
use support\Request;

/**
 * 权限中间件
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-04-29
 */
class AuthMiddleware
{
    /**
     * 鉴权检测
     * @param \support\Request $request
     * @param \Closure $next
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function handle(Request $request, Closure $next)
    {
        $request->token = null;
        $request->user = null;
        $AdminLog = new SystemAdminLog;
        $AdminLog->action_ip = $request->ip();
        $AdminLog->path = $request->baseUrl();
        $AdminLog->params = $request->param();
        # 检测是否具有权限
        try {
            self::canAccess($request);
            if ($request->user) {
                $AdminLog->admin_id = $request->user['id'];
                $AdminLog->role_id = $request->user['role_id'];
            }
            $AdminLog->save();
        } catch (\Throwable $th) {
            if ($request->user) {
                $AdminLog->admin_id = $request->user['id'];
                $AdminLog->role_id = $request->user['role_id'];
            }
            $AdminLog->save();
            throw $th;
        }
        # 检测通过
        $response = $next($request);
        return $response;
    }


    /**
     * 检测是否有权限
     * @param \support\Request $request
     * @throws \Exception
     * @return bool
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private static function canAccess(Request $request): bool
    {
        $control  = '';
        $action  = '';
        $pathinfo = explode('/', $request->pathinfo());
        if (isset($pathinfo[0])) {
            $control = $pathinfo[0];
        }
        if (isset($pathinfo[1])) {
            $action = $pathinfo[1];
        }
        // 无控制器地址
        if (!$control) {
            return true;
        }
        // 获取控制器鉴权信息
        $class = app()->getNamespace() . '\\controller\\' . $control . 'Controller';
        $class = new \ReflectionClass($class);
        $properties = $class->getDefaultProperties();
        $noNeedLogin = $properties['noNeedLogin'] ?? [];
        $noNeedAuth = $properties['noNeedAuth'] ?? [];

        // 不需要登录
        if (in_array($action, $noNeedLogin)) {
            return true;
        }
        // 获取登录信息
        $authorization = $request->header('Authorization', '');
        if (empty($authorization)) {
            throw new Exception('请先登录', 12000);
        }
        // 获取用户信息
        try {
            $token      = str_replace('Bearer ', '', $authorization);
            $user       = Auth::decrypt($token);
        } catch (\Throwable $e) {
            throw new Exception('登录已过期，请重新登录', 12000);
        }
        if (!$user) {
            throw new Exception('用户信息获取失败', 12000);
        }
        $request->token = $authorization;
        $request->user = $user;
        // 不需要鉴权
        if (in_array($action, $noNeedAuth)) {
            return true;
        }
        if (empty($user['role']['is_system'])) {
            throw new Exception('操作权限出错', 404);
        }
        // 系统级部门，不需要鉴权
        if ($user['role']['is_system'] === '20') {
            return true;
        }
        // 获取角色规则
        $rule = AuthMgr::getAdminRoleColumn($user);
        // 检测是否有操作权限
        $ctrlName = str_replace('Controller', '', basename(str_replace('\\', '/', $control)));
        $path = "{$ctrlName}/{$action}";
        if (!in_array($path, $rule) && $path !== 'Updated/updateCheck') {
            throw new Exception('没有该操作权限', 404);
        }
        return true;
    }
}
