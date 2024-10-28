<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 权限规则-请求类型
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-07
 */
class AuthRuleMethods extends Enum
{
    const GET = [
        'text'      => 'GET',
        'value'     => 'GET',
    ];
    const POST = [
        'text'      => 'POST',
        'value'     => 'POST',
    ];
    const PUT = [
        'text'      => 'PUT',
        'value'     => 'PUT',
    ];
    const DELETE = [
        'text'      => 'DELETE',
        'value'     => 'DELETE',
    ];
}
