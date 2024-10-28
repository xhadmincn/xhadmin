<?php
namespace app\common\enum;

use app\common\Enum;

class IsSystem extends Enum
{
    // 非系统
    const SYSTEM_NO = [
        'text'      => '非系统',
        'value'     => '10'
    ];
    // 系统参数
    const SYSTEM_YES = [
        'text'      => '系统',
        'value'     => '20'
    ];
}
