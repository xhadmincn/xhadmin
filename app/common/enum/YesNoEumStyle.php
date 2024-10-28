<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * YES/NO 枚举类
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class YesNoEumStyle extends Enum
{
    const STATUS_NO = [
        'text'      => 'danger',
        'value'     => '10',
    ];
    const STATUS_YES = [
        'text'      => 'success',
        'value'     => '20',
    ];
}
