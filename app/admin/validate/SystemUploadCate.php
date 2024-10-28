<?php

namespace app\admin\validate;

use think\Validate;

class SystemUploadCate extends Validate
{
    protected $rule =   [
        'title'             => 'require',
        'dir_name'          => 'require|alpha',
        'sort'              => 'require|number',
    ];

    protected $message  =   [
        'title.require'     => '请输入分类名称',
        'dir_name.require'  => '请输入目录名称',
        'dir_name.alpha'    => '目录名称只能是字母',
        'sort.require'      => '请输入分类排序',
        'sort.number'       => '分类排序只能是数字',
    ];

    protected $scene = [
        'add'               =>  ['title', 'dir_name', 'sort'],
        'edit'              =>  ['title', 'sort'],
    ];
}
