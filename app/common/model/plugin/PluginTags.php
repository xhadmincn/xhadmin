<?php

namespace app\common\model\plugin;

/**
 * 单页系统
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-11
 */
class PluginTags extends AppidModel
{
    public $append = [
        'link'
    ];

    /**
     * 获取单页标签链接
     * @param mixed $value
     * @param mixed $data
     * @return string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function getLinkAttr($value,$data)
    {
        $link = '';
        if (!empty($data['saas_appid']) && !empty($data['name'])) {
            $request = request();
            $url     = $request->domain();
            $link    = "{$url}/tags/?appid={$data['saas_appid']}&name={$data['name']}";
        }
        return $link;
    }
}
