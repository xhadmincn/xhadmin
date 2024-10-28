<?php

namespace app\admin\validate;

use think\Validate;

class SystemAdminRole extends Validate
{
    protected $rule =   [
        'title'             => 'require',
    ];

    protected $message  =   [
        'title.require'     => '请输入部门名称',
    ];

    protected $scene = [
        'add'               =>  ['title'],
        'edit'              =>  ['title'],
    ];
}
