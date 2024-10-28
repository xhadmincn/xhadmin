<?php

namespace plugin\{TEAM_PLUGIN_NAME}\api;

use app\common\model\plugin\PluginAdmin;
use support\Request;
use think\facade\Session;
use think\helper\Str;

/**
 * 登录接口
 * @author 贵州猿创科技有限公司
 * @copyright (c) 2023
 */
class Login
{
    /**
     * 站点商登录
     * @param \support\Request $request
     * @param int $appid
     * @return array
     * @author John
     */
    public function login(Request $request, int $appid)
    {
        $adminModel = PluginAdmin::with(['role'])->where(['saas_appid' => $appid, 'pid' => 0])->find();
        if (!$adminModel) {
            throw new \Exception("管理员不存在，请联系站长");
        }

        $sessionId = Str::random(32);
        Session::set('{TEAM_PLUGIN_NAME}', $adminModel);

        // 更新登录信息
        $ip = $request->ip();
        $adminModel->last_login_ip = $ip;
        $adminModel->last_login_time = date('Y-m-d H:i:s');
        $adminModel->save();

        $loginUrl  = "/app/{TEAM_PLUGIN_NAME}/admin/#/?token={$sessionId}&appid={$appid}";
        $data = [
            'url'    => $loginUrl
        ];
        return $data;
    }
}
