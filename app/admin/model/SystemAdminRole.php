<?php

namespace app\admin\model;

use app\common\model\SystemAdminRole as ModelSystemAdminRole;

/**
 * 部门角色
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class SystemAdminRole extends ModelSystemAdminRole
{
    /**
     * 获取角色组件选项
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-09
     * @param  integer $admin_id
     * @return array
     */
    public static function selectOptions(): array
    {
        $where = [
            'is_system'       => '10'
        ];
        $field = [
            'id as value',
            'title as label'
        ];
        $list = self::where($where)
            ->field($field)
            ->select()
            ->toArray();
        return $list;
    }
}
