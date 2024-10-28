<?php

namespace app\common\validate;

use think\Validate;

/**
 * 七牛云附件库验证
 * @author 贵州猿创科技有限公司
 * @copyright (c) 2023
 */
class QiniuValidate extends Validate
{
    protected $rule =   [
        'access_key'                => 'require',
        'secret_key'                => 'require',
        'bucket'                    => 'require',
        'domain'                    => 'require',
    ];

    protected $message  =   [
        'accessKey.require'         => '请配置附件设置-七牛云AccessKey',
        'secretKey.require'         => '请配置附件设置-七牛云SecretKey',
        'bucket.require'            => '请配置附件设置-七牛云Bucket',
        'domain.require'            => '请配置附件设置-七牛云域名',
    ];
}
