<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 小程序基本配置枚举类
 * @author 贵州猿创科技有限公司
 * @copyright (c) 贵州猿创科技有限公司
 */
class AppletPlatform extends Enum
{
    // 微信小程序
    const WECHAT_MINI = [
        'text'      => '微信小程序',
        'value'     => 'mini_wechat'
    ];
    // 抖音小程序
    const DOUYIN_MINI = [
        'text'      => '抖音小程序',
        'value'     => 'douyin'
    ];
}
