<?php

namespace app\store\model;


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
     * 获取请求方法
     * @param mixed $value
     * @return mixed
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    protected function getMethodAttr($value)
    {
        return is_array($value) ? current($value) : 'GET';
    }
}
