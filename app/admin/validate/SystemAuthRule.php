<?php

namespace app\admin\validate;

use think\Validate;

class SystemAuthRule extends Validate
{
    protected $rule = [
        'title'     => 'require',
        'pid'       => 'require',
        'component' => 'verifyComponet',
        'path'      => 'require',
        'method'    => 'require',
    ];

    protected $message = [
        'title.require'     => '请输入菜单名称',
        'pid.require'       => '请选择父级菜单',
        'path.require'      => '请输入权限路由',
        'method.require'    => '请选择请求类型',
    ];

    protected $scene = [
        'add'  => [
            'pid',
            'title',
            'component',
            'path',
        ],
        'edit' => [
            'pid',
            'title',
            'component',
            'path',
        ],
    ];

    /**
     * 根据组件选择数据验证
     * @param mixed $value
     * @param mixed $rule
     * @param mixed $data
     * @return bool|string
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-30
     */
    public function verifyComponet($value, $rule, $data)
    {
        // 远程组件
        if ($data['component'] === 'remote/index') {
            if (!isset($data['auth_params']) || !$data['auth_params']) {
                return '请输入远程组件地址';
            }
        }
        return true;
    }
}