<?php

namespace app\admin\validate;

use think\Validate;

class Modules extends Validate
{
    protected $rule =   [
        'table_name'                => 'require',
        'table_comment'             => 'require',
    ];

    protected $message  =   [
        'table_name.require'        => '请输入表名称',
        'table_comment.require'     => '请输入表备注',
    ];
}
