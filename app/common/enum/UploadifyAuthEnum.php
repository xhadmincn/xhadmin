<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 附件库权限枚举类
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class UploadifyAuthEnum extends Enum
{
    const NO_AUTH = [
        'text'      => '无本地权限',
        'value'     => '10',
        'disabled'  => false
    ];
    const YES_AUTH = [
        'text'      => '有本地权限',
        'value'     => '20',
        'disabled'  => false
    ];
}
