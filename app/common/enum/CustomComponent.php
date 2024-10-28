<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 自定义组件类型
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class CustomComponent extends Enum
{
    const AMAP = [
        'text'      => '高德地图',
        'value'     => 'amap'
    ];
    const BMAP = [
        'text'      => '百度地图',
        'value'     => 'bmap'
    ];
    const QMAP = [
        'text'      => '腾讯地图',
        'value'     => 'qmap'
    ];
    const REMOTE = [
        'text'      => '远程组件',
        'value'     => 'remote'
    ];
    const UPLOADIFY = [
        'text'      => '附件库',
        'value'     => 'uploadify'
    ];
    const INFO = [
        'text'      => '信息展示',
        'value'     => 'info'
    ];
    const HCODE = [
        'text'      => '代码展示',
        'value'     => 'HCode'
    ];
    const PROMPTTIP = [
        'text'      => '隐藏框',
        'value'     => 'PromptTip'
    ];
    const WANGEDITOR = [
        'text'      => 'wangEditor',
        'value'     => 'wangEditor'
    ];
    const TINYMCEEDITOR = [
        'text'      => 'tinymceEditor',
        'value'     => 'tinymceEditor'
    ];
    const XINPUT = [
        'text'      => '自定义输入框',
        'value'     => 'XInput'
    ];
    const NDivider = [
        'text'      => '虚线框',
        'value'     => 'NDivider'
    ];
    const XTABLE = [
        'text'      => '自定义表格',
        'value'     => 'XTable'
    ];
}
