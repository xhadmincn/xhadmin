<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 小程序基本数据
 * @author 贵州猿创科技有限公司
 * @copyright (c) 贵州猿创科技有限公司
 */
class AppletMiniSettins extends Enum
{
    const CONFIGS = [
        [
            'title'         => '小程序配置',
            'name'          => 'system_mini_config',
            'icon'          => '',
            'sort'          => 100,
            'layout_col'    => '20',
            'children'      => [
                [
                    'title'         => 'appid',
                    'name'          => 'applet_appid',
                    'value'         => '',
                    'component'     => 'input',
                    'extra'         => [],
                    'placeholder'   => 'appid',
                    'sort'          => 100,
                    'show'          => '10'
                ],
                [
                    'title'         => 'secret',
                    'name'          => 'applet_secret',
                    'value'         => '',
                    'component'     => 'input',
                    'extra'         => [],
                    'placeholder'   => 'secret',
                    'sort'          => 100,
                    'show'          => '10'
                ],
                [
                    'title'         => 'privatekey',
                    'name'          => 'applet_privatekey',
                    'value'         => '',
                    'component'     => 'input',
                    'extra'         => [],
                    'placeholder'   => 'privatekey',
                    'sort'          => 100,
                    'show'          => '10'
                ],
                [
                    'title'         => '显示预览',
                    'name'          => 'applet_state',
                    'value'         => '10',
                    'component'     => 'input',
                    'extra'         => [],
                    'placeholder'   => 'applet_state',
                    'sort'          => 100,
                    'show'          => '10'
                ],
            ],
        ],
    ];
}
