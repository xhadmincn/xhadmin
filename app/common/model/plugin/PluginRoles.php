<?php

namespace app\common\model\plugin;

/**
 * 角色权限
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-11
 */
class PluginRoles extends AppidModel
{
    # 设置JSON字段转换
    protected $json = ['rule'];
    # 设置JSON数据返回数组
    protected $jsonAssoc = true;

    /**
     * 获取角色组件选项
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-09
     * @param  integer $admin_id
     * @return array
     */
    public function selectOptions(int $saas_appid): array
    {
        $where = [
            'saas_appid'       => $saas_appid
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


    /**
     * 验证是否可删除
     * @param int $role_id
     * @return bool
     * @author John
     */
    public function verifyDelete(int $role_id)
    {
        $model = PluginAdmin::where('id', $role_id)->count();
        if ($model) {
            return true;
        }
        return false;
    }
}
