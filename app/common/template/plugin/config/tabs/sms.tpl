<?php

# 短信配置
return [
    'title' => '默认使用短信方式',
    'value' => 'aliyun',
    'type' => 'radio',
    'extra' => [
        'options' => [
            [
                'label' => '阿里云短信',
                'value' => 'aliyun',
            ],
            [
                'label' => '腾讯云短信',
                'value' => 'qcloud',
            ],
        ],
        'control' => [
            [
                'value' => 'aliyun',
                'where' => '==',
                'col'   => 12,
                'rule'  => [
                    [
                        # 配置项标识(可通过getHpconfig进行读取)
                        'name' => 'aliyun.accessKeyId',
                        # 配置项名称
                        'title' => 'accessKeyId',
                        # 表单组件(参考common/enum/FormType.php)
                        'component' => 'input',
                        # 数据默认值
                        'value' => '',
                    ],
                    [
                        # 配置项标识(可通过getHpconfig进行读取)
                        'name' => 'aliyun.accessKeySecret',
                        # 配置项名称
                        'title' => 'accessKeySecret',
                        # 表单组件(参考common/enum/FormType.php)
                        'component' => 'input',
                        # 数据默认值
                        'value' => '',
                    ],
                    [
                        # 配置项标识(可通过getHpconfig进行读取)
                        'name' => 'aliyun.TemplateCode',
                        # 配置项名称
                        'title' => '短信模板',
                        # 表单组件(参考common/enum/FormType.php)
                        'component' => 'input',
                        # 数据默认值
                        'value' => '',
                    ],
                    [
                        # 配置项标识(可通过getHpconfig进行读取)
                        'name' => 'aliyun.signName',
                        # 配置项名称
                        'title' => '短信签名',
                        # 表单组件(参考common/enum/FormType.php)
                        'component' => 'input',
                        # 数据默认值
                        'value' => '',
                    ],
                ],
            ],
            [
                'value' => 'qcloud',
                'where' => '==',
                'rule' => [
                    [
                        # 配置项标识(可通过getHpconfig进行读取)
                        'name' => 'qcloud.accessKeyId',
                        # 配置项名称
                        'title' => 'accessKeyId',
                        # 表单组件(参考common/enum/FormType.php)
                        'component' => 'input',
                        # 数据默认值
                        'value' => '',
                    ],
                ],
            ],
        ],
    ],
];