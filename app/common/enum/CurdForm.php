<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * CURD表单控件
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class CurdForm extends Enum
{
    const INPUT = [
        'text'      => '输入框',
        'value'     => 'input',
    ];
    const SELECT = [
        'text'      => '选择器',
        'value'     => 'select',
    ];
    const SWITCH = [
        'text'      => '开关组件',
        'value'     => 'switch',
    ];
    const DATE = [
        'text'      => '日期选择',
        'value'     => 'date',
    ];
    const DATETIME = [
        'text'      => '日期时间',
        'value'     => 'dateTime',
    ];
    const TIME = [
        'text'      => '时间选择',
        'value'     => 'time',
    ];
    const RADIO = [
        'text'      => '单选框',
        'value'     => 'radio',
    ];
    const TEXTAREA = [
        'text'      => '文本框',
        'value'     => 'textarea',
    ];
    const ICONS = [
        'text'      => '图标库',
        'value'     => 'icons',
    ];
    const UPLOADIFY = [
        'text'      => '附件库',
        'value'     => 'uploadify',
    ];
    const REMOTE = [
        'text'      => '远程组件',
        'value'     => 'remote',
    ];
}
