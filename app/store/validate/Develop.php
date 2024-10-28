<?php

namespace app\store\validate;

use think\Validate;

class Develop extends Validate
{
    protected $rule             =   [
        'title'                 => 'require',
        'platform'              => 'require',
        'team'                  => 'require',
        'name'                  => 'require',
        'is_auth'               => 'require',
        'username'              => 'require',
        'password'              => 'require',
        'logo'                  => 'require',
    ];

    protected $message          =   [
        'title.require'         => '请输入项目名称',
        'platform.require'      => '请选择项目类型',
        'team.require'          => '请输入团队标识',
        'name.require'          => '请输入应用标识',
        'is_auth.require'       => '是否需要权限管理',
        'username.require'      => '请输入超管账号',
        'password.require'      => '请输入登录密码',
        'logo.require'          => '请选择项目LOGO',
    ];
}
