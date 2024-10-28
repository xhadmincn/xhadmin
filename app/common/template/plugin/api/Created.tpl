<?php

namespace plugin\{TEAM_PLUGIN_NAME}\api;

use app\common\model\plugin\PluginAdmin;
use app\common\model\plugin\PluginRoles;
use think\facade\Db;

class Created
{
    /**
     * 读取管理员数据
     * @param int $appid
     * @return mixed
     * @author John
     */
    public function read(int $appid)
    {
        $adminModel = PluginAdmin::with(['role'])->where(['saas_appid' => $appid, 'pid' => 0])->find();
        if (!$adminModel) {
            throw new \Exception("管理员不存在，请联系站长");
        }
        return $adminModel->toArray();
    }

    /**
     * 创建或编辑管理员
     * @param array $data
     * @return void
     * @author John
     */
    public function createAdmin(array $data)
    {
        if (empty($data['username'])) {
            throw new \Exception("请设置管理员帐号");
        }
        Db::startTrans();
        try {
            $where       = [
                'saas_appid'        => $data['id'],
                'pid'               => 0,
                'is_system'         => '20'
            ];
            $SystemAdmin = PluginAdmin::where($where)->find();
            if (!$SystemAdmin) {
                if(empty($data['password'])){
                    throw new \Exception("请设置管理员密码");
                }
                $SystemAdmin = new PluginAdmin;
            }
            $where = [
                'saas_appid'        => $data['id'],
                'pid'               => 0,
                'is_system'         => '20',
            ];
            $SystemAdminRole = PluginRoles::where($where)->find();
            if (!$SystemAdminRole) {
                $SystemAdminRole = new PluginRoles;
                $SystemAdminRole->saas_appid = $data['id'];
                $SystemAdminRole->pid = 0;
                $SystemAdminRole->is_system = '20';
                $SystemAdminRole->title = '系统管理员';
                $SystemAdminRole->rule = null;
            }
            $SystemAdminRole->save();
            $SystemAdmin->saas_appid = $data['id'];
            $SystemAdmin->role_id = $SystemAdminRole->id;
            $SystemAdmin->pid = 0;
            $SystemAdmin->username = $data['username'];
            if(!empty($data['password'])){
                $SystemAdmin->password = $data['password'];
            }
            $SystemAdmin->status = '20';
            $SystemAdmin->nickname = '超级管理员';
            $SystemAdmin->is_system = '20';
            $SystemAdmin->save();
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            throw $e;
        }
    }
}
