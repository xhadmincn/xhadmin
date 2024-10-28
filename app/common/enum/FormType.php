<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 配置项表单类型
 * 支持如下 'input','select','radio','checkbox','textarea','uploadify'
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-06
 */
class FormType extends Enum
{
    const HIDDEN = [
        'text'      => '隐藏框',
        'value'     => 'input'
    ];
    const INPUT = [
        'text'      => '输入框',
        'value'     => 'input'
    ];
    const TEXTAREA = [
        'text'      => '文本框',
        'value'     => 'textarea'
    ];
    const SELECT = [
        'text'      => '选择框',
        'value'     => 'select'
    ];
    const RADIO = [
        'text'      => '单选框',
        'value'     => 'radio'
    ];
    const CHECKBOX = [
        'text'      => '复选框',
        'value'     => 'checkbox'
    ];
    const UPLOADIFY = [
        'text'      => '附件库',
        'value'     => 'uploadify'
    ];
}
