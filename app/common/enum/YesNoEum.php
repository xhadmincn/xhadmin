<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * YES/NO 枚举类
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class YesNoEum extends Enum
{
    const API_NO = [
        'text'      => '否',
        'value'     => '10',
    ];
    const SHOW_YES = [
        'text'      => '是',
        'value'     => '20',
    ];
}
