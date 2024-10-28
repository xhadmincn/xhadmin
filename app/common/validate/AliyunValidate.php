<?php

namespace app\common\validate;

use think\Validate;

/**
 * 阿里云附件库验证
 * @author 贵州猿创科技有限公司
 * @copyright (c) 2023
 */
class AliyunValidate extends Validate
{
    protected $rule =   [
        'access_id'                 => 'require',
        'access_secret'             => 'require',
        'bucket'                    => 'require',
        'endpoint'                  => 'require',
    ];

    protected $message  =   [
        'access_id.require'         => '请配置附件设置-阿里云AccessKeyId',
        'access_secret.require'     => '请配置附件设置-阿里云AccessKeySecret',
        'bucket.require'            => '请配置附件设置-阿里云Bucket',
        'endpoint.require'          => '请配置附件设置-阿里云Endpoint',
    ];
}
