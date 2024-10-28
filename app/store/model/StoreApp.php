<?php

namespace app\store\model;

/**
 * 应用模型
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-05-03
 */
class StoreApp extends \app\common\model\StoreApp
{
    // 定义全局查询范围
    protected $globalScope = ['store'];

    /**
     * 基类查询
     * @param mixed $query
     * @return void
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public function scopeStore($query)
    {
        $store_id = request()->user['id'];
        $query->where('store_id', $store_id);
    }
}