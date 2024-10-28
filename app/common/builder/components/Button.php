<?php

namespace app\common\builder\components;

use app\common\builder\ListBuilder;

/**
 * 表格按钮配置
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-02-27
 */
trait Button
{
    /**
     * 表格顶部按钮
     * @var array
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private $topButtonList = [];

    /**
     * 开启选择按钮操作
     * @var array
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private $selectButtonList = [];

    /**
     * 表格列按钮
     * @var array
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private $rightButtonList = [];

    /**
     * 开启右侧操作选项
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-02-28
     * @param  string      $title
     * @param  array       $extra
     * @return ListBuilder
     */
    public function addActionOptions(string $title, array $extra = []): ListBuilder
    {
        $field = 'rightButtonList';
        $extra = array_merge([
            'width'             => 'auto',
            'fixed'             => 'right',
            'slots'             => [
                'default'       => $field
            ],
            'params'            => [
                # 是否显示更多按钮
                'group'                 => false,
                # 更多按钮文本
                'groupText'             => '',
                # 更多按钮图标
                'buttonGroupIcon'       => '',
                # 更多按钮图标类型：element / @vicons/antd（默认）
                'buttonGroupIconType'   => '',
                # 按钮样式，参考element-plug
                'buttonStyle'           => [
                    'link'              => false,
                ],
            ],
        ], $extra);
        $this->addColumn($field, $title, $extra);
        return $this;
    }

    /**
     * 添加表格头部按钮
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-01
     * @param  string      $field
     * @param  string      $title
     * @param  array       $pageData
     * @return ListBuilder
     */
    public function addTopButton(string $field, string $title, array $pageData = [], array $message = [], array $button = []): ListBuilder
    {
        $btnData = $this->checkUsedAttrs(
            $field,
            $title,
            $pageData,
            $message,
            $button
        );
        // 设置按钮
        $this->topButtonList[] = $btnData;
        return $this;
    }

    /**
     * 添加底部按钮
     * @param string $field
     * @param string $title
     * @param array $pageData
     * @param array $message
     * @param array $button
     * @return \app\common\builder\ListBuilder
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function addBottomButton(string $field, string $title, array $pageData = [], array $message = [], array $button = []): ListBuilder
    {
        $btnData = $this->checkUsedAttrs(
            $field,
            $title,
            $pageData,
            $message,
            $button
        );
        // 设置按钮
        $this->selectButtonList[] = $btnData;
        return $this;
    }

    /**
     * 添加右侧列按钮
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-02-28
     * @param  string      $field
     * @param  string      $title
     * @param  array       $pageData
     * @param  array       $message
     * @param  array       $button
     * @return ListBuilder
     */
    public function addRightButton(string $field, string $title, array $pageData = [], array $message = [], array $button = []): ListBuilder
    {
        $btnData = $this->checkUsedAttrs(
            $field,
            $title,
            $pageData,
            $message,
            $button
        );
        # 别名参数，仅右侧按钮
        $btnData['pageData']['aliasParams'] = [];
        $btnData['pageData']                = array_merge($btnData['pageData'], $pageData);
        # 处理别名转换
        foreach ($btnData['pageData']['aliasParams'] as $key => $value) {
            if (is_numeric($key)) {
                $btnData['pageData']['aliasParams'][$value] = $value;
                unset($btnData['pageData']['aliasParams'][$key]);
            }
        }
        // 设置按钮
        $this->rightButtonList[] = $btnData;
        return $this;
    }

    /**
     * 获取按钮通用参数
     * @param string $field
     * @param string $title
     * @param array $pageData
     * @param array $message
     * @param array $button
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-18
     */
    private function checkUsedAttrs(string $field, string $title, array $pageData = [], array $message = [], array $button = []): array
    {
        $btnData = $this->getBtnDefaultAttrs($field, $title);
        // 合并页面数据
        $btnData['pageData'] = array_merge($btnData['pageData'], $pageData);
        // 处理模态框参数
        $this->getModalAttrs($btnData);
        // 合并消息数据
        $btnData['message'] = array_merge($btnData['message'], $message);
        // 默认的按钮样式
        $btnData['button'] = array_merge($this->getBtnDefaultStyle(), $button);

        //返回按钮
        return $btnData;
    }

    /**
     * 获取按钮默认参数
     * @param string $field
     * @param string $title
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-18
     */
    private function getBtnDefaultAttrs(string $field, string $title): array
    {
        $data = [
            'field'    => $field,
            'title'    => $title,
            'pageData' => [
                /**
                 * 按钮组件类型
                 * page：跳转页面
                 * link：跳转链接
                 * confirm：确认框
                 * table：模态框-表格
                 * modal：模态框-表单
                 * remote：模态框-远程组件
                 * info：模态框-详情数据
                 */
                'type'        => 'page',
                // 是否支持返回
                'isBack'      => true,
                // 请求API
                'api'         => '',
                // 请求类型
                'method'      => 'GET',
                // 跳转路径
                'path'        => '',
                // 附带参数
                'queryParams' => [],
                // 右侧按钮别名参数(仅支持右侧按钮)
                'aliasParams' => [],
            ],
            // 按钮样式
            'button'   => [],
            // type为[modal，table，remote，info]时有效
            'message'  => [
                'title' => '温馨提示',
            ],
        ];
        return $data;
    }

    /**
     * 合并模态框参数
     * @param array $btnData
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-18
     */
    private function getModalAttrs(array &$btnData): array
    {
        // 设置模态框专有属性
        if (in_array($btnData['pageData']['type'], ['modal', 'table', 'remote','info'])) {
            $btnData['message']['customStyle']['width']     = '45%';
            $btnData['message']['customStyle']['height']    = '650px';
            $btnData['message']['closeOnClickModal']        = false;
            $btnData['message']['showConfirmButton']        = true;
        }
        // 设置确认框专有属性
        if ($btnData['pageData']['type'] === 'confirm') {
        }
        return $btnData;
    }

    /**
     * 获得默认按钮样式
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-02-28
     * @param  string $text
     * @param  string $type
     * @param  string $size
     * @return array
     */
    private function getBtnDefaultStyle(string $type = 'default', string $size = ''): array
    {
        $data = [
            //类型 default / primary / success / warning / danger / info / text
            'type'       => $type,
            //尺寸 medium / small / mini
            'size'       => $size,
            //是否朴素按钮
            'plain'      => false,
            //是否圆角按钮
            'round'      => false,
            //是否圆形按钮
            'circle'     => false,
            //是否加载中状态
            'loading'    => false,
            //是否禁用状态
            'disabled'   => false,
            //图标类名
            'icon'       => '',
            //是否默认聚焦
            'autofocus'  => false,
            //原生 type 属性
            'nativeType' => "button",
            // 是否显示按钮
            'show'       => true,
            // 是否文字链接
            'link'       => false,
        ];
        return $data;
    }
}