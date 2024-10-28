<?php

namespace app\admin\validate;

use think\Validate;

class SystemConfig extends Validate
{
    protected $rule =   [
        'title'             => 'require',
        'name'              => 'require',
        'component'         => 'require',
    ];

    protected $message  =   [
        'title.require'             => '配置项标识不能为空',
        'name.require'              => '配置项名称不能为空',
        'component.require'         => '表单组件不能为空',
    ];
}
