<?php
namespace app\common\trait\config;

use app\common\enum\CustomComponent;
use app\common\builder\FormBuilder;
use Exception;

/**
 * 表单trait工具类
 * @author 贵州猿创科技有限公司
 * @copyright (c) 2023
 */
trait FormUtil
{
    /**
     * 应用ID（null则获取系统配置）
     * @var string|null
     */
    protected $saas_appid = null;

    /**
     * 扩展组件类型
     * @var array
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $extraOptions = ['checkbox', 'radio', 'select'];

    /**
     * 获取表单规则
     * @param array $data
     * @return \app\common\builder\FormBuilder
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    private function getFormView(array $data): FormBuilder
    {
        $builder   = new FormBuilder;
        # 获取自定义组件枚举列
        $custComponent = CustomComponent::getColumn('value');
        foreach ($data as $value) {
            # 虚线框不验证
            if ($value['component'] !== 'NDivider') {
                # 数据验证
                hpValidate(\app\admin\validate\SystemConfig::class, $value);
                # 验证扩展组件
                if (in_array($value['component'], $this->extraOptions)) {
                    if (empty($value['extra'])) {
                        throw new Exception("[{$value['title']}] - 扩展【extra】数据不能为空");
                    }
                    if (empty($value['extra']['options'])) {
                        throw new Exception("[{$value['title']}] - 扩展的【options】选项数据不能为空");
                    }
                }
            }
            #设置默认值
            $configValue = !isset($value['value']) ? '' : $value['value'];
            # 设置扩展数据
            $configExtra = empty($value['extra']) ? [] : $value['extra'];
            # 虚线框
            if ($value['component'] === 'NDivider') {
                $builder->addDivider($value['title'], $configExtra);
                continue;
            }
            # 设置组件参数
            if (in_array($value['component'], $custComponent)) {
                # 自定义组件
                $builder->addComponent(
                    $value['name'] ?? '',
                    $value['component'],
                    $value['title'],
                    $configValue,
                    $configExtra
                );
            } else {
                # 普通组件
                $builder->addRow(
                    $value['name'] ?? '',
                    $value['component'],
                    $value['title'],
                    $configValue,
                    $configExtra
                );
            }
        }
        # 获取表单句柄
        return $builder;
    }
    
    /**
     * 获取选项卡规则
     * @param string $active
     * @param array $data
     * @return \app\common\builder\FormBuilder
     * @author John
     */
    private function getTabsView(string $active,array $data):FormBuilder
    {
        # 实例表单构建器
        $builder = new FormBuilder;
        $builder = $builder->initTabsActive('active', $active, [
            'props'                 => [
                // 选项卡样式
                'type'              => 'line',
                'tabPosition'       => 'top',
            ],
        ]);
        foreach ($data as $value) {
            if (empty($value['name'])) {
                throw new Exception('分组标识不能为空');
            }
            if (empty($value['title'])) {
                throw new Exception('分组名称不能为空');
            }
            if (empty($value['children'])) {
                throw new Exception('配置项数据错误');
            }
            if (empty($value['col'])) {
                $value['col'] = 24;
            }
            # 获取表单
            $formRow = $this->getFormView($value['children'], $value['col'])->getBuilder()->formRule();
            # 设置表单项
            $builder->addTab(
                $value['name'],
                $value['title'],
                $formRow
            );
        }
        # 结束选项卡
        $builder->endTabs();
        # 返回构建器
        return $builder;
    }

    /**
     * 获取分割线规则
     * @param array $data
     * @return \app\common\builder\FormBuilder
     */
    private function getDividerView(array $data)
    {
        # 实例表单构建器
        $builder = new FormBuilder;
        foreach ($data as $value) {
            if (empty($value['name'])) {
                throw new Exception('分组标识不能为空');
            }
            if (empty($value['title'])) {
                throw new Exception('分组名称不能为空');
            }
            if (empty($value['children'])) {
                throw new Exception('配置项数据错误');
            }
            if (empty($value['col'])) {
                $value['col'] = 24;
            }
            # 获取额外扩展
            $extra = [];
            if (!empty($value['divider'])) {
                $extra = $value['divider'];
            }
            # 获取虚线规则
            $dividerRule = $builder->addDivider($value['title'],$extra)->getBuilder()->formRule();
            # 获取表单规则
            $formRule = $this->getFormView($value['children'] ?? [], $value['col'])->getBuilder()->formRule();
            # 合并表单规则
            $rules = array_merge($dividerRule, $formRule);
            # 设置规则
            $builder->getBuilder()->setRule($rules);
        }
        # 返回构建器
        return $builder;
    }

    /**
     * 获取组件规则
     * @param string $active
     * @param array $component
     * @throws \Exception
     * @return FormBuilder
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private function getComponentView(string $active,array $component)
    {
        # 设置默认选中
        $active = empty($active) ? $component['value'] : $active;
        # 设置组件类型
        $componentType = $component['type'] ?? 'radio';
        # 设置组件标题
        $title = $component['title'] ?? '组件规则错误';
        # 设置额外扩展
        $extra = $component['extra'] ?? [];
        if (empty($extra['control'])) {
            throw new Exception('组件的【control】选项数据不能为空');
        }
        # 设置组件选项
        foreach ($extra['control'] as $key => $value) {
            $col = $value['col'] ?? 24;
            $extra['control'][$key]['rule'] = $this->getFormView($value['rule'] ?? [],$col)->getBuilder()->formRule();
        }
        # 实例表单构建器
        $builder = new FormBuilder;
        # 设置组件
        $builder = $builder->addRow('active', $componentType, $title, $active, $extra);
        # 返回构建器
        return $builder;
    }
}