<?php

namespace app\common\manager;

use Exception;
use app\common\model\SystemAdminRole;
use app\common\model\SystemAuthRule;

/**
 * 权限管理器
 * @author 贵州猿创科技有限公司
 * @copyright (c) 贵州猿创科技有限公司
 */
class AuthMgr
{
    // 设置允许输出字段
    private static $visible = [
        'id',
        'pid',
        'path',
        'title',
        'method',
        'component',
        'auth_params',
        'icon',
        'show',
    ];

    /**
     * 获取管理员菜单与Vue路由
     * @param array $admin
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function run(array $admin): array
    {
        // 获取管理员权限
        $adminRoleRule = self::getAdminRoleRule($admin);
        return $adminRoleRule;
    }

    /**
     * 获取部门权限
     * @param array $admin
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function getAdminRoleRule(array $admin): array
    {
        $where     = [
            'id' => $admin['role_id']
        ];
        $roleModel = SystemAdminRole::where($where)
            ->field(['rule', 'is_system'])
            ->find();
        if (!$roleModel) {
            throw new Exception('该部门不存在');
        }
        // 已授权规则
        $rule = $roleModel->rule;
        // 默认查询全部权限
        $model = SystemAuthRule::order(['sort' => 'asc', 'id' => 'asc']);
        if ($roleModel->is_system != '20') {
            // 普通级部门（查询已授权规则）
            $model->whereIn('path', $rule);
        }
        $data = $model->field(self::$visible)
            ->select()
            ->each(function ($e) {
                $e->show   = $e->show === '20' ? '1' : '0';
                $e->method = current($e->method);
                return $e;
            })
            ->toArray();

        // 返回数据
        return $data;
    }

    /**
     * 获取部门权限规则列表
     * @param array $admin
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function getAdminRoleColumn(array $admin): array
    {
        $rule = self::getAdminRoleRule($admin);
        $data = [];
        foreach ($rule as $key => $value) {
            $data[$key] = $value['path'];
        }
        return $data;
    }

    /**
     * 递归查询父级规则
     *
     * @param string $rule
     * @return array
     */
    private static function getParentRule(array &$list, string $rule): array
    {
        $where = [
            ['id', '=', $rule],
        ];
        $model = SystemAuthRule::where($where)
            ->field(self::$visible)
            ->find();
        if (!$model) {
            throw new Exception("{$rule}，父级规则不存在");
        }
        $data = $model->toArray();
        array_push($list, $data);
        if ($data['pid']) {
            self::getParentRule($list, $data['pid']);
        }
        return $list;
    }
}