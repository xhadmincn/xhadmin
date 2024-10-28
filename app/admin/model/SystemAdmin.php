<?php

namespace app\admin\model;

use app\common\model\SystemAdmin as ModelSystemAdmin;
use app\common\model\SystemAdminRole;
use app\common\service\UploadService;

/**
 * 管理员
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-04-29
 */
class SystemAdmin extends ModelSystemAdmin
{
    /**
     * 关联等级
     * @return \think\model\relation\HasOne
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function role()
    {
        return $this->hasOne(SystemAdminRole::class, 'id', 'role_id');
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
    
    /**
     * 获取管理员组件选项
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-09
     * @param  integer $admin_id
     * @return array
     */
    public static function selectOptions(int $admin_id): array
    {
        $where = [
            ['pid', '=', $admin_id],
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
     * @param int $admin_id
     * @return array
     * @author John
     */
    public static function selectRoleOptions(int $admin_id): array
    {
        $where = [
            ['pid', '=', $admin_id],
        ];
        $field = [
            'id as value',
            'username as label'
        ];
        $list  = SystemAdminRole::selectOptions($admin_id);
        return $list;
    }
}
