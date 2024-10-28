<?php

namespace app\common\validate;

use think\Validate;

/**
 * 腾讯云附件库验证
 * @author 贵州猿创科技有限公司
 * @copyright (c) 2023
 */
class QcloudValidate extends Validate
{
    protected $rule =   [
        'region'                    => 'require',
        'domain'                    => 'require',
        'app_id'                    => 'require',
        'bucket'                    => 'require',
        'secret_id'                 => 'require',
        'secret_key'                => 'require',
    ];

    protected $message  =   [
        'region.require'            => '请配置附件设置-腾讯云地域',
        'domain.require'            => '请配置附件设置-腾讯云域名',
        'app_id.require'            => '请配置附件设置-腾讯云AppId',
        'bucket.require'            => '请配置附件设置-腾讯云Bucket',
        'secret_id.require'         => '请配置附件设置-腾讯云SecretId',
        'secret_key.require'        => '请配置附件设置-腾讯云SecretKey',
    ];
}
