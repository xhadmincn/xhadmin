<?php

namespace app\common\model\plugin;

use app\common\Model as BaseModel;

/**
 * 应用模型
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-05-03
 */
class AppidModel extends BaseModel
{
    // 定义全局查询范围
    protected $globalScope = ['appid'];

    /**
     * 基类查询
     * @param mixed $query
     * @return void
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public function scopeAppid($query)
    {
        $saas_appid = request()->appid;
        if ($saas_appid) {
            $query->where('saas_appid', $saas_appid);
        }
    }

    /**
     * 模型事件-插入前
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function onBeforeInsert($model)
    {
        $saas_appid = request()->appid;
        if ($saas_appid) {
            $model->saas_appid = $saas_appid;
        }
    }
}