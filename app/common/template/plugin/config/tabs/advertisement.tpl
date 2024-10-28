<?php

# 流量主配置
return [
    [
        'title'         => '微信流量主配置',
        'name'          => 'weixin_ad',
        'children'      => [
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'weixin_banner_status',
                # 配置项名称
                'title' => 'banner广告状态',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'radio',
                # 数据默认值
                'value' => '0',
                # 额外扩展
                'extra' => [
                    'options' => [
                        ['label' => '关闭', 'value' => '0'],
                        ['label' => '开启', 'value' => '1'],
                    ]
                ],
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'weixin_banner_ad_id',
                # 配置项名称
                'title' => 'banner广告ID',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'input',
                # 数据默认值
                'value' => '',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'weixin_video_status',
                # 配置项名称
                'title' => '视频广告状态',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'radio',
                'extra' => [
                    'options' => [
                        ['label' => '关闭', 'value' => '0'],
                        ['label' => '开启', 'value' => '1'],
                    ]
                ],
                # 数据默认值
                'value' => '0',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'weixin_video_ad_id',
                # 配置项名称
                'title' => '视频广告ID',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'input',
                # 数据默认值
                'value' => '',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'weixin_incentive_status',
                # 配置项名称
                'title' => '激励广告状态',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'radio',
                'extra' => [
                    'options' => [
                        ['label' => '关闭', 'value' => '0'],
                        ['label' => '开启', 'value' => '1'],
                    ]
                ],
                # 数据默认值
                'value' => '0',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'weixin_incentive_ad_id',
                # 配置项名称
                'title' => '激励广告ID',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'input',
                # 数据默认值
                'value' => '',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'weixin_interstitial_status',
                # 配置项名称
                'title' => '插屏广告状态',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'radio',
                'extra' => [
                    'options' => [
                        ['label' => '关闭', 'value' => '0'],
                        ['label' => '开启', 'value' => '1'],
                    ]
                ],
                # 数据默认值
                'value' => '0',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'weixin_interstitial_ad_id',
                # 配置项名称
                'title' => '插屏广告ID',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'input',
                # 数据默认值
                'value' => '',
            ],
        ],
    ],
    [
        'title'         => '微抖音流量主配置',
        'name'          => 'douyin_ad',
        'children'      => [
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'douyin_banner_status',
                # 配置项名称
                'title' => 'banner广告状态',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'radio',
                # 数据默认值
                'value' => '0',
                # 额外扩展
                'extra' => [
                    'options' => [
                        ['label' => '关闭', 'value' => '0'],
                        ['label' => '开启', 'value' => '1'],
                    ]
                ],
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'douyin_banner_ad_id',
                # 配置项名称
                'title' => 'banner广告ID',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'input',
                # 数据默认值
                'value' => '',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'douyin_video_status',
                # 配置项名称
                'title' => '视频广告状态',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'radio',
                'extra' => [
                    'options' => [
                        ['label' => '关闭', 'value' => '0'],
                        ['label' => '开启', 'value' => '1'],
                    ]
                ],
                # 数据默认值
                'value' => '0',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'douyin_video_ad_id',
                # 配置项名称
                'title' => '视频广告ID',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'input',
                # 数据默认值
                'value' => '',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'douyin_incentive_status',
                # 配置项名称
                'title' => '激励广告状态',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'radio',
                'extra' => [
                    'options' => [
                        ['label' => '关闭', 'value' => '0'],
                        ['label' => '开启', 'value' => '1'],
                    ]
                ],
                # 数据默认值
                'value' => '0',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'douyin_incentive_ad_id',
                # 配置项名称
                'title' => '激励广告ID',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'input',
                # 数据默认值
                'value' => '',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'douyin_interstitial_status',
                # 配置项名称
                'title' => '插屏广告状态',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'radio',
                'extra' => [
                    'options' => [
                        ['label' => '关闭', 'value' => '0'],
                        ['label' => '开启', 'value' => '1'],
                    ]
                ],
                # 数据默认值
                'value' => '0',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'douyin_interstitial_ad_id',
                # 配置项名称
                'title' => '插屏广告ID',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'input',
                # 数据默认值
                'value' => '',
            ],
        ],
    ],
];