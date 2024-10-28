<?php

namespace app\common\service;

use app\common\model\SystemAuthRule;
use app\common\enum\AuthRuleRuleType;
use app\common\utils\Data;

/**
 * 规则额外逻辑
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-07
 */
class AuthRuleService
{
    /**
     * 获取全部递归完成数据
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @return array
     */
    public static function getAllRule(): array
    {
        $list = SystemAuthRule::order(['sort' => 'asc', 'id' => 'asc'])
            ->select()
            ->toArray();
        $list = Data::channelLevel($list, '', '', 'path');
        $list = self::parseRule($list);
        return $list;
    }

    /**
     * 重组规则数据
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array $data
     * @return array
     */
    private static function parseRule(array $data): array
    {
        $list = [];
        $i = 0;
        foreach ($data as $value) {
            $list[$i] = $value;
            if (isset($value['children']) && $value['children']) {
                $list[$i]['children'] = self::parseRule($value['children']);
            }
            $i++;
        }
        return $list;
    }

    /**
     * 获取cascader组件数据
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @return array
     */
    public static function getCascaderOptions(): array
    {
        $orderBy = ['sort'=>'asc','id'=>'asc'];
        $list   = SystemAuthRule::order($orderBy)->select()->toArray();
        $list   = Data::channelLevel($list, 0, '', 'id', 'pid');
        $list   = self::getChildrenOptions($list);
        $list = array_merge([
            [
                'label' => '顶级权限菜单',
                'value' => 0
            ]
        ], $list);
        return $list;
    }

    /**
     * 递归拼接cascader组件数据
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array $data
     * @return array
     */
    public static function getChildrenOptions(array $data): array
    {
        $list = [];
        $i = 0;
        foreach ($data as $value) {
            $componentText                  = AuthRuleRuleType::getText($value['component']);
            $title                          = "{$value['title']}-{$componentText}";
            $list[$i]['label']              = $title;
            $list[$i]['value']              = $value['id'];
            $list[$i]['disabled']           = false;
            if (!empty($value['disabled'])) {
                $list[$i]['disabled']       = true;
            }
            if ($value['children']) {
                $list[$i]['children']       = self::getChildrenOptions($value['children']);
            }
            $i++;
        }
        return $list;
    }
}
