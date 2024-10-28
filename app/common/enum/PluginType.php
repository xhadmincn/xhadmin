<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 应用插件类型
 * 支持类型：'app','plgin'
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-22
 */
class PluginType extends Enum
{
    const APP = [
        'text'      => '应用',
        'value'     => 'app'
    ];
    const PLUGIN = [
        'text'      => '插件',
        'value'     => 'plugin'
    ];
}
