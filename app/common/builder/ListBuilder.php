<?php

namespace app\common\builder;

use app\common\builder\components\Button;
use app\common\builder\components\RealTable;
use app\common\builder\components\Screen;
use app\common\builder\components\Table;

/**
 * 表格构造器
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-02-27
 */
class ListBuilder
{
    # 表格设置
    use Table;
    # 表格按钮
    use Button;
    # 表格筛选
    use Screen;
    # 实时表格
    use RealTable;

    // 在每个对象的静态缓存中存储现有属性。
    protected static $cache = [];

    /**
     * 初始化表格
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-02-28
     */
    public function __construct()
    {
    }

    /**
     * 解析表格规则
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-02-27
     * @return array
     */
    private function parseRule(): array
    {
        return get_object_vars($this);
    }

    /**
     * 获取表格JSON规则
     * @return \think\response\Json
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function JSONRule()
    {
        return json($this->parseRule());
    }

    /**
     * 返回表格规则
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-02-27
     * @return array
     */
    public function create(): array
    {
        $resutl = $this->parseRule();
        # 实时表格
        if (isset($resutl['realTable'])) {
            $data['realTable'] = $resutl['realTable'];
            unset($resutl['realTable']);
        }
        # 筛选查询
        if (!empty($resutl['formConfig'])) {
            array_push(
                $resutl['formConfig']['items'],
                [
                    'itemRender' => [
                        'name' => '$buttons',
                        'children'  => $resutl['screenConfig']
                    ],
                ]
            );
        }
        $data['tableConfig'] = $resutl;
        # 移除按钮属性
        if (isset($data['tableConfig']['topButtonList'])) {
            unset($data['tableConfig']['topButtonList']);
        }
        if (isset($data['tableConfig']['selectButtonList'])) {
            unset($data['tableConfig']['selectButtonList']);
        }
        if (isset($data['tableConfig']['rightButtonList'])) {
            unset($data['tableConfig']['rightButtonList']);
        }
        if (isset($data['tabsConfig']['tabsConfig'])) {
            unset($data['tabsConfig']['tabsConfig']);
        }
        $data['tabsConfig']             = $resutl['tabsConfig'];
        $data['topButtonList']          = $resutl['topButtonList'];
        $data['selectButtonList']       = $resutl['selectButtonList'];
        $data['rightButtonList']        = $resutl['rightButtonList'];
        # 返回数据
        return $data;
    }

    /**
     * 动态设置属性
     * @param mixed $name
     * @param mixed $value
     * @return void
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}