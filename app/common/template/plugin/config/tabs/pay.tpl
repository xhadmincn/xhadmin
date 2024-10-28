<?php

# 支付配置
return [
    [
        'title'         => '微信支付',
        'name'          => 'wxpay',
        # 仅虚线模式有效
        // 'divider'       => [
        //     'borderStyle'       => 'dashed',
        // ],
        'col'           => 12,
        'children'      => [
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'wxpay_mch_id',
                # 配置项名称
                'title' => '微信商户号',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'input',
                # 数据默认值
                'value' => '',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'wxpay_mch_key',
                # 配置项名称
                'title' => '支付密钥',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'input',
                # 数据默认值
                'value' => '',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'wxpay_ssl_cer',
                # 配置项名称
                'title' => '商户PEM证书CERT路径',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'uploadify',
                # 数据默认值
                'value' => '',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'wxpay_ssl_key',
                # 配置项名称
                'title' => '商户PEM证书KEY路径',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'uploadify',
                # 数据默认值
                'value' => '',
            ]
        ],
    ],
    [
        'title'         => '支付宝支付',
        'name'          => 'alipay',
        'col'           => 12,
        'children'      => [
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'alipay_mch_id',
                # 配置项名称
                'title' => '商户号',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'input',
                # 数据默认值
                'value' => '',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'alipay_mch_key',
                # 配置项名称
                'title' => '支付密钥',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'input',
                # 数据默认值
                'value' => '',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'alipay_ssl_cer',
                # 配置项名称
                'title' => '商户PEM证书CERT路径',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'uploadify',
                # 数据默认值
                'value' => '',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'name' => 'alipay_ssl_key',
                # 配置项名称
                'title' => '商户PEM证书KEY路径',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'uploadify',
                # 数据默认值
                'value' => '',
            ]
        ],
    ],
];