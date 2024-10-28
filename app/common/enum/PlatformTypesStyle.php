<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 应用平台类型
 * 支持类型：'wechat','mini_wechat','douyin','h5','app','other'
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-22
 */
class PlatformTypesStyle extends Enum
{
    const WECHAT = [
        'text'      => 'success',
        'value'     => 'wechat'
    ];
    const MINI_WECHAT = [
        'text'      => 'success',
        'value'     => 'mini_wechat'
    ];
    const DOUYIN = [
        'text'      => 'info',
        'value'     => 'douyin'
    ];
    const H5 = [
        'text'      => 'error',
        'value'     => 'h5'
    ];
    const APP = [
        'text'      => 'error',
        'value'     => 'app'
    ];
    const OTHER = [
        'text'      => 'warning',
        'value'     => 'other'
    ];
}
