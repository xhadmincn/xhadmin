<?php

namespace app\admin\validate;

use think\Validate;

class Fields extends Validate
{
    protected $rule =   [
        'name'                      => 'require',
        'comment'                   => 'require',
        'type'                      => 'require',
    ];

    protected $message  =   [
        'name.require'              => '请输入字段名称',
        'comment.require'           => '请输入字段注释',
        'type.require'              => '请选择字段类型',
    ];
}
