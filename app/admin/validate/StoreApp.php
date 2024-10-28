<?php

namespace app\admin\validate;

use think\Validate;

class StoreApp extends Validate
{
    protected $rule =   [
        'title'                     => 'require',
        'url'                       => 'require|url',
        'logo'                      => 'require',
    ];

    protected $message  =   [
        'title.require'             => '请输入项目名称',
        'url.require'               => '请输入项目域名',
        'url.url'                   => '请输入正确的域名',
        'logo.require'              => '请上传项目图标',
    ];
}
