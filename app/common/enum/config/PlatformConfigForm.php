<?php

namespace app\common\enum\config;

use app\common\enum\WechatType;

/**
 * 平台配置表单
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-05-04
 */
class PlatformConfigForm
{
    /**
     * 获取平台配置表单
     * @param string $key
     * @return mixed
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-04
     */
    public static function getConfig(string $key)
    {
        $config = self::parseConfig();
        return isset($config[$key]) ? $config[$key] : [];
    }

    /**
     * 获取配置列表数据
     * @param string $key
     * @return array<array>
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getConfigData(string $key)
    {
        $config = self::parseConfig();
        $platformConfig = isset($config[$key]) ? $config[$key] : [];
        $data           = [];
        foreach ($platformConfig['list'] as $tabs) {
            if (isset($tabs['children']) && is_array($tabs['children'])) {
                foreach ($tabs['children'] as $value) {
                    if ($value['type'] !== 'n-divider') {
                        $data[] = [
                            'field'         => $value['field'],
                            'form_type'     => $value['type'],
                            'value'         => $value['value']
                        ];
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 通用配置
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-05
     */
    private static function baseConfig()
    {
        return [
            [
                'type'  => 'input',
                'field' => 'web_name',
                'title' => '平台名称',
                'value' => '',
                'extra' => [
                    'col' => [
                        'span' => 12
                    ],
                ]
            ],
            [
                'type'  => 'input',
                'field' => 'domain',
                'title' => '平台域名',
                'value' => '',
                'extra' => [
                    'col' => [
                        'span' => 12
                    ],
                ]
            ],
            [
                'type'  => 'uploadify',
                'field' => 'logo',
                'title' => '平台图标',
                'value' => '',
                'extra' => [
                    'col' => [
                        'span' => 12
                    ],
                ]
            ],
            [
                'type'  => 'textarea',
                'field' => 'description',
                'title' => '平台描述',
                'value' => '',
                'extra' => [
                    'col' => [
                        'span' => 12
                    ],
                ]
            ],
        ];
    }

    # 通用支付配置
    private static function parsePay()
    {
        return [
            'label'    => '支付设置',
            'value'    => 'pay',
            'children' => [
                [
                    'type'  => 'n-divider',
                    'field' => 'wechat_pay',
                    'title' => '',
                    'value' => '',
                    'extra' => [
                        'children'  => [
                            'default'       => '微信支付V2',
                        ],
                    ]
                ],
                [
                    'type'  => 'input',
                    'field' => 'mch_id',
                    'title' => '微信商户号',
                    'value' => '',
                    'extra' => [
                        'col' => [
                            'span' => 12
                        ],
                    ]
                ],
                [
                    'type'  => 'input',
                    'field' => 'mch_key',
                    'title' => '支付密钥',
                    'value' => '',
                    'extra' => [
                        'col' => [
                            'span' => 12
                        ],
                    ]
                ],
                [
                    'type'  => 'input',
                    'field' => 'appid',
                    'title' => 'APPID',
                    'value' => '',
                    'extra' => [
                        'col' => [
                            'span' => 12
                        ],
                    ]
                ],
                [
                    'type'  => 'uploadify',
                    'field' => 'wechat_cart_file',
                    'title' => 'cert证书',
                    'value' => '',
                    'extra' => [
                        'col'   => [
                            'span' => 6
                        ],
                        'props' => [
                            'format' => ['cart', 'pem']
                        ],
                    ]
                ],
                [
                    'type'  => 'uploadify',
                    'field' => 'wechat_key_file',
                    'title' => 'key证书',
                    'value' => '',
                    'extra' => [
                        'col'   => [
                            'span' => 6
                        ],
                        'props' => [
                            'format' => ['key', 'pem']
                        ],
                    ]
                ],
                [
                    'type'  => 'n-divider',
                    'field' => 'alipay',
                    'title' => '',
                    'value' => '',
                    'extra' => [
                        'children'  => [
                            'default'       => '支付宝配置',
                        ],
                    ]
                ],
                [
                    'type'  => 'input',
                    'field' => 'alipay_appid',
                    'title' => 'APPID',
                    'value' => '',
                    'extra' => [
                    ]
                ],
                [
                    'type'  => 'uploadify',
                    'field' => 'alipay_public_key',
                    'title' => '支付宝公钥',
                    'value' => '',
                    'extra' => [
                        'col' => [
                            'span' => 8
                        ],
                        'props' => [
                            'format' => ['pem']
                        ],
                    ]
                ],
                [
                    'type'  => 'uploadify',
                    'field' => 'alipay_app_public_key',
                    'title' => '应用公钥',
                    'value' => '',
                    'extra' => [
                        'col' => [
                            'span' => 8
                        ],
                        'props' => [
                            'format' => ['pem']
                        ],
                    ]
                ],
                [
                    'type'  => 'uploadify',
                    'field' => 'alipay_app_private_key',
                    'title' => '应用私钥',
                    'value' => '',
                    'extra' => [
                        'col' => [
                            'span' => 8
                        ],
                        'props' => [
                            'format' => ['pem']
                        ],
                    ]
                ],
            ],
        ];
    }

    # 通用短信配置
    private static function parseSms()
    {
        return [
            'label'    => '短信配置',
            'value'    => 'sms',
            'children' => [
                [
                    'type'  => 'n-divider',
                    'field' => 'aliyun_sms',
                    'title' => '',
                    'value' => '',
                    'extra' => [
                        'children'  => [
                            'default'       => '阿里云短信',
                        ],
                    ]
                ],
                [
                    'type'  => 'input',
                    'field' => 'aliyun_access_key_id',
                    'title' => 'accessKeyId',
                    'value' => '',
                    'extra' => [
                        'col' => [
                            'span' => 12
                        ],
                    ]
                ],
                [
                    'type'  => 'input',
                    'field' => 'aliyun_accessKey_secret',
                    'title' => 'accessKeySecret',
                    'value' => '',
                    'extra' => [
                        'col' => [
                            'span' => 12
                        ],
                    ]
                ],
                [
                    'type'  => 'input',
                    'field' => 'aliyun_sms_tpl_id',
                    'title' => '通用短信模板ID',
                    'value' => '',
                    'extra' => [
                    ]
                ],
                // [
                //     'type'  => 'n-divider',
                //     'field' => 'qq_sms',
                //     'title' => '',
                //     'value' => '',
                //     'extra' => [
                //         'children'  => [
                //             'default'       => '腾讯云短信',
                //         ],
                //     ]
                // ],
                // [
                //     'type'  => 'input',
                //     'field' => 'qq_access_key_id',
                //     'title' => 'accessKeyId',
                //     'value' => '',
                //     'extra' => [
                //         'col' => [
                //             'span' => 12
                //         ],
                //     ]
                // ],
                // [
                //     'type'  => 'input',
                //     'field' => 'qq_accessKey_secret',
                //     'title' => 'accessKeySecret',
                //     'value' => '',
                //     'extra' => [
                //         'col' => [
                //             'span' => 12
                //         ],
                //     ]
                // ],
            ],
        ];
    }

    /**
     * 获取表单配置
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-04
     */
    private static function parseConfig()
    {
        return [
            // 微信公众号
            'wechat'      => [
                'active' => 'setting',
                'list'   => [
                    [
                        'label'    => '平台设置',
                        'value'    => 'setting',
                        'children' => array_merge(
                            self::baseConfig(),
                            [
                                [
                                    'type'  => 'select',
                                    'field' => 'wechat_type',
                                    'title' => '公众号类型',
                                    'value' => '0',
                                    'extra' => [
                                        'col'     => [
                                            'span' => 12
                                        ],
                                        'options' => WechatType::parseAlias('label')
                                    ]
                                ],
                                [
                                    'type'  => 'input',
                                    'field' => 'original_appid',
                                    'title' => '原始ID',
                                    'value' => '',
                                    'extra' => [
                                        'col' => [
                                            'span' => 12
                                        ],
                                    ]
                                ],
                                [
                                    'type'  => 'input',
                                    'field' => 'wechat_appid',
                                    'title' => 'APPID',
                                    'value' => '',
                                    'extra' => [
                                        'col' => [
                                            'span' => 12
                                        ],
                                    ]
                                ],
                                [
                                    'type'  => 'input',
                                    'field' => 'wechat_app_secret',
                                    'title' => 'appSecret',
                                    'value' => '',
                                    'extra' => []
                                ],
                            ]
                        ),
                    ],
                    [
                        'label'    => '公众号通信',
                        'value'    => 'wechat_message',
                        'children' => [
                            [
                                'type'  => 'n-tag',
                                'field' => 'wechat_api_url',
                                'title' => '通信地址',
                                'value' => '',
                                'extra' => [
                                    'props'    => [
                                        'type' => 'warning',
                                    ],
                                    'children' => ['default' => '']
                                ]
                            ],
                            [
                                'type'  => 'n-tag',
                                'field' => 'wechat_token',
                                'title' => 'token',
                                'value' => '',
                                'extra' => [
                                    'props'    => [
                                        'type' => 'warning',
                                    ],
                                    'children' => ['default' => '']
                                ]
                            ],
                            [
                                'type'  => 'n-tag',
                                'field' => 'wechat_encoding_aes_key',
                                'title' => 'encodingAESKey',
                                'value' => '',
                                'extra' => [
                                    'props'    => [
                                        'type' => 'warning',
                                    ],
                                    'children' => ['default' => '']
                                ]
                            ],
                        ],
                    ],
                    self::parsePay(),
                    self::parseSms(),
                ],
            ],
            // 微信小程序
            'mini_wechat' => [
                'active' => 'setting',
                'list'   => [
                    [
                        'label'    => '平台设置',
                        'value'    => 'setting',
                        'children' => self::baseConfig(),
                    ],
                    self::parsePay(),
                    self::parseSms(),
                ],
            ],
            // 抖音应用
            'douyin'      => [
                'active' => 'setting',
                'list'   => [
                    [
                        'label'    => '平台设置',
                        'value'    => 'setting',
                        'children' => self::baseConfig(),
                    ],
                    self::parsePay(),
                    self::parseSms(),
                ],
            ],
            // 网页应用
            'h5'          => [
                'active' => 'setting',
                'list'   => [
                    [
                        'label'    => '平台设置',
                        'value'    => 'setting',
                        'children' => self::baseConfig(),
                    ],
                    self::parsePay(),
                    self::parseSms(),
                ],
            ],
            // APP应用
            'app'         => [
                'active' => 'setting',
                'list'   => [
                    [
                        'label'    => '平台设置',
                        'value'    => 'setting',
                        'children' => self::baseConfig(),
                    ],
                    self::parsePay(),
                    self::parseSms(),
                ],
            ],
            // 其他应用
            'other'       => [
                'active' => 'setting',
                'list'   => [
                    [
                        'label'    => '平台设置',
                        'value'    => 'setting',
                        'children' => self::baseConfig(),
                    ],
                    self::parsePay(),
                    self::parseSms(),
                ],
            ],
        ];
    }
}