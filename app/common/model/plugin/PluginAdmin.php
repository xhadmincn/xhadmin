<?php

namespace app\common\model\plugin;

use app\common\utils\Password;
use app\common\service\UploadService;

/**
 * 管理员模型
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-11
 */
class PluginAdmin extends AppidModel
{
    # 隐藏字段
    protected $hidden = [
        'password'
    ];

    /**
     * 关联等级
     * @return \think\model\relation\HasOne
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function role()
    {
        return $this->hasOne(PluginRoles::class, 'id', 'role_id');
    }

    /**
     * 获取头像URL
     * @param mixed $value
     * @return string
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function getHeadimgAttr($value)
    {
        return $value ? UploadService::url((string)$value) : '';
    }
    public function setHeadimgAttr($value)
    {
        return $value ? UploadService::path($value) : $value;
    }

    /**
     * 密码加密写入
     * @param mixed $value
     * @return bool|string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    protected function setPasswordAttr($value)
    {
        if (!$value) {
            return false;
        }
        return Password::passwordHash((string)$value);;
    }

    /**
     * 获取管理员角色规则
     * @author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-11
     */
    public static function getAdminRule(int $adminId)
    {
        return [];
    }

    /**
     * 获取管理员组件选项
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-09
     * @param  integer $admin_id
     * @return array
     */
    public function selectOptions(int $saas_appid): array
    {
        $where = [
            ['saas_appid', '=', $saas_appid],
        ];
        $field = [
            'id as value',
            'username as label'
        ];
        $list = self::where($where)
            ->field($field)
            ->select()
            ->toArray();
        return $list;
    }

    /**
     * 获取角色组件选项
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-09
     * @param  integer $admin_id
     * @return array
     */
    public function selectRolesOptions(int $saas_appid): array
    {
        return (new PluginRoles)->selectOptions($saas_appid);
    }
}
