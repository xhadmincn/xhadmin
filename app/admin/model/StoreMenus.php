<?php

namespace app\admin\model;

use app\common\enum\AuthRuleRuleType;
use app\common\utils\Data;

/**
 * 租户菜单
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-12
 */
class StoreMenus extends \app\common\model\StoreMenus
{
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
        $orderBy = ['sort' => 'asc', 'id' => 'asc'];
        $list    = StoreMenus::order($orderBy)->select()->toArray();
        $list    = Data::channelLevel($list, 0, '', 'id', 'pid');
        $list    = self::getChildrenOptions($list);
        $list    = array_merge([
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
    private static function getChildrenOptions(array $data): array
    {
        $list = [];
        $i    = 0;
        foreach ($data as $value) {
            $componentText     = AuthRuleRuleType::getText($value['component']);
            $title             = "{$value['title']}-{$componentText}";
            $list[$i]['label'] = $title;
            $list[$i]['value'] = $value['id'];
            if ($value['children']) {
                $list[$i]['children'] = self::getChildrenOptions($value['children']);
            }
            $i++;
        }
        return $list;
    }
}
