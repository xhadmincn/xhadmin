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
class WechatType extends Enum
{
    const WECHAT_NOT = [
        'text'                      => '未认证订阅号',
        'value'                     => '0',
    ];
    const WECHAT_NOT_SERVICE = [
        'text'                      => '未认证服务号',
        'value'                     => '1',
    ];
    const WECHAT_CERT = [
        'text'                      => '已认证订阅号',
        'value'                     => '2',
    ];
    const WECHAT_CERT_SERVICE = [
        'text'                      => '已认证服务号',
        'value'                     => '3',
    ];
}
