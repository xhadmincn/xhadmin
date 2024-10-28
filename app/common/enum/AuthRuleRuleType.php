<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 权限规则-组件类型 枚举类
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class AuthRuleRuleType extends Enum
{
    const DIR_VIEW = [
        'text'      => '不使用组件',
        'value'     => 'none/index',
    ];
    const FORM_VIEW = [
        'text'      => '表单组件',
        'value'     => 'form/index',
    ];
    const TABLE_VIEW = [
        'text'      => '表格组件',
        'value'     => 'table/index',
    ];
    const REMOTE_VIEW = [
        'text'      => '远程Vue组件',
        'value'     => 'remote/index',
    ];
    const VUE_VIEW = [
        'text'      => '渲染Vue组件',
        'value'     => 'vue/index',
    ];
    const HTML_PAGE = [
        'text'      => 'HTML页面',
        'value'     => 'html/index',
    ];
}
