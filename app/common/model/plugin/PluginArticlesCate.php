<?php

namespace app\common\model\plugin;

/**
 * 文章分类
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-11
 */
class PluginArticlesCate extends AppidModel
{
    public $append = [
        'link'
    ];

    /**
     * 关联旗下文章
     * @return \think\model\relation\HasMany
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function articles()
    {
        $where = [
            'status'    => '20'
        ];
        return $this->hasMany(PluginArticles::class, 'cid', 'id')->where($where);
    }

    /**
     * 获取链接
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
        if (!empty($data['saas_appid'])) {
            $request = request();
            $url     = $request->domain();
            $link    = "{$url}/Articles/category?appid={$data['saas_appid']}&cid={$data['id']}";
        }
        return $link;
    }
}
