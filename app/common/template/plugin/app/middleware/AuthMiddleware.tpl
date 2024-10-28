<?php
namespace plugin\{TEAM_PLUGIN_NAME}\app\middleware;

use think\Request;
use think\Response;
use loong\oauth\facade\Auth;

/**
 * 根模块权限中间件
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-11
 */
class AuthMiddleware
{
    /**
    * 中间件入口
    * @author 贵州猿创科技有限公司
    * @Email 416716328@qq.com
    * @DateTime 2023-03-11
    */
    public function handle(Request $request, callable $handler): Response
    {
        // 鉴权检测
        try {
            $this->canAccess($request);
            $response = $request->method() == 'OPTIONS' ? response('', 204) : $handler($request);
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
        $noIdentifyApp = $properties['noNeedAppid'] ?? [];
        # 设置全局Appid
        $request->appid = $request->header('Appid');
        if (!in_array($request->action(), $noIdentifyApp)) {
            if (!$request->appid) {
                throw new \Exception('访问应用不存在', 404);
            }
        }
        # 不需要登录
        if (in_array($request->action(), $noNeedLogin)) {
            return true;
        }
        # 令牌验证
        $user = Auth::decrypt($request->header('Authorization'));
        if(!$user){
            throw new \Exception('登录已过期，请重新登录', 12000);
        }
        $request->userModel             = $user;
        $request->userId                = $user['id'];
        $request->saas_appid            = $user['saas_appid'];

        # 不需要鉴权
        if (in_array($request->action(), $noNeedAuth)) {
            return true;
        }
        return true;
    }
}