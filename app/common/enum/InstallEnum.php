<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 是否安装枚举类
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class InstallEnum extends Enum
{
    const NO = [
        'text'      => '未安装',
        'value'     => '10',
    ];
    const YES = [
        'text'      => '已安装',
        'value'     => '20',
    ];
}
