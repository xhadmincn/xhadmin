<?php

namespace app\common\builder\components;

use app\common\builder\ListBuilder;

/**
 * 表格筛选配置
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-02-27
 */
trait Screen
{
    # 筛选表单
    protected $formConfig = [];
    # 筛选表单配置
    protected $screenConfig = [
        [
            'props' => [
                'api' => '',
                'method' => 'GET',
                'type' => 'submit',
                'status' => 'primary',
                'content' => '查询',
            ],
        ],
        [
            'props' => [
                'api' => '',
                'method' => 'GET',
                'type' => 'reset',
                'status' => 'default',
                'content' => '重置',
            ],
        ]
    ];

    /**
     * 设置启用远程组件表单
     * @param string $remote
     * @param array $params
     * @return \app\common\builder\ListBuilder
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function screenRemote(string $remote, array $params = []): ListBuilder
    {
        $this->screenRemote['file']       = $remote;
        $this->screenRemote['ajaxParams'] = $params;
        return $this;
    }

    /**
     * 添加筛选单元格
     * @param string $field
     * @param string $type
     * @param string $title
     * @param array $extra
     * @return \app\common\builder\ListBuilder
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function addScreen(string $field, string $type, string $title, array $extra = []): ListBuilder
    {
        if (!isset($this->formConfig['items'])) {
            $this->formConfig['data']  = [];
            $this->formConfig['items'] = [];
        }
        $item = [
            'field' => $field,
            'title' => $title,
            'itemRender' => [
                'name' => "\${$type}",
                'props' => [
                    'placeholder' => "请输入{$title}"
                ],
            ],
        ];
        if ($extra && is_array($extra)) {
            $item['itemRender']['props'] = $extra;
        }
        array_push($this->formConfig['items'], $item);
        $this->formConfig['data'][$field] = '';
        return $this;
    }

    /**
     * 设置提交按钮
     * @param array $config
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function submitConfig(array $config)
    {
        $this->screenConfig = array_merge($this->screenConfig[0]['props'], $config);
        return $this;
    }

    /**
     * 设置重置按钮
     * @param array $config
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function restConfig(array $config)
    {
        $this->screenConfig = array_merge($this->screenConfig[1]['props'], $config);
        return $this;
    }

    /**
     * 筛选表单配置
     * @param array $config
     * @return \app\common\builder\ListBuilder
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function screenConfig(array $config): ListBuilder
    {
        $this->screenConfig = array_merge($this->screenConfig, $config);
        return $this;
    }
}