<?php

# 基本配置
return [
    [
        'name'          => 'web_name',
        'title'         => '应用名称',
        'value'         => '',
        'component'     => 'input',
        'extra'         => [
            'props'     => [
                'type'  => 'text',
                'placeholder'   => '请输入网站名称',
            ],
        ],
    ],
    [
        'name'          => 'web_logo',
        'title'         => '应用图标',
        'value'         => '',
        'component'     => 'uploadify',
        'extra'         => [],
    ],
];
