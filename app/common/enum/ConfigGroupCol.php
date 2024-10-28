<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 配置分组布局模式
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-06
 */
class ConfigGroupCol extends Enum
{
    const ONE = [
        'text'      => '单列',
        'value'     => '10',
        'col'       => 24,
    ];
    const TWO = [
        'text'      => '二列',
        'value'     => '20',
        'col'       => 12,
    ];
    const THREE = [
        'text'      => '四列',
        'value'     => '30',
        'col'       => 6
    ];
}
