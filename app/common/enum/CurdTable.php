<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * CURD表格控件
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class CurdTable extends Enum
{
    const NONE = [
        'text'      => '不展示',
        'value'     => '',
    ];
    const TEXT = [
        'text'      => '文本展示',
        'value'     => 'text',
    ];
    const ASSETS = [
        'text'      => '资产组件',
        'value'     => 'assets',
    ];
    const ICONS = [
        'text'      => '图标组件',
        'value'     => 'icons',
    ];
    const IMAGE = [
        'text'      => '图片组件',
        'value'     => 'image',
    ];
    const PHOTOS = [
        'text'      => '图册组件',
        'value'     => 'images',
    ];
    const INPUT = [
        'text'      => '输入框组件',
        'value'     => 'input',
    ];
    const MONEY = [
        'text'      => '金额组件',
        'value'     => 'money',
    ];
    const REMOTE = [
        'text'      => '远程组件',
        'value'     => 'remote',
    ];
    const SELECT = [
        'text'      => '选择器组件',
        'value'     => 'select',
    ];
    const SWITCH = [
        'text'      => '开关组件',
        'value'     => 'switch',
    ];
    const TAGS = [
        'text'      => '标签组件',
        'value'     => 'tags',
    ];
}
