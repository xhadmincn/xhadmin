<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 显示状态通用枚举
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class ShowStatusStyle extends Enum
{
    const SHOW_NO = [
        'text'      => 'danger',
        'value'     => '10',
    ];
    const SHOW_YES = [
        'text'      => 'success',
        'value'     => '20',
    ];
}
