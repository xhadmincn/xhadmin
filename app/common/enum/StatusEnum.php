<?php

namespace app\common\enum;

use app\common\Enum;

/**
 * 状态管理器
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-08
 */
class StatusEnum extends Enum
{
    const STATUS_NO = [
        'text'      => '禁用',
        'value'     => '10',
    ];
    const STATUS_YES = [
        'text'      => '正常',
        'value'     => '20',
    ];
}
