<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 权限规则-组件类型样式 枚举类
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class AuthRuleRuleTypeStyle extends Enum
{
    const DIR_VIEW = [
        'text'      => '',
        'value'     => 'none/index',
    ];
    const FORM_VIEW = [
        'text'      => 'warning',
        'value'     => 'form/index',
    ];
    const TABLE_VIEW = [
        'text'      => 'success',
        'value'     => 'table/index',
    ];
    const REMOTE_VIEW = [
        'text'      => 'info',
        'value'     => 'remote/index',
    ];
}
