<?php

namespace app\common\model\plugin;

use app\common\service\UploadService;

/**
 * 广告系统
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-11
 */
class PluginAds extends AppidModel
{
    /**
     * 设置图片储存
     * @param mixed $value
     * @return array|string
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    protected function setImageUrlAttr($value)
    {
        return $value ? UploadService::path($value) : '';
    }

    /**
     * 解析图片URL
     * @param mixed $value
     * @return string
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    protected function getImageUrlAttr($value)
    {
        return $value ? UploadService::url((string) $value) : '';
    }
}
