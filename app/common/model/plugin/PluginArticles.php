<?php

namespace app\common\model\plugin;

use app\common\service\UploadService;

/**
 * 文章内容
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-11
 */
class PluginArticles extends AppidModel
{
    public $append = [
        'link'
    ];

    /**
     * 关联分类
     * @return \think\model\relation\HasOne
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function category()
    {
        return $this->hasOne(PluginArticlesCate::class, 'id', 'cid');
    }

    /**
     * 获取分类选项
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function getCategoryOptions()
    {
        $fields = [
            'id as value',
            'title as label'
        ];
        $where  = [];
        $data = PluginArticlesCate::where($where)->field($fields)->select()->toArray();
        return $data;
    }

    /**
     * 获取文章链接
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
            $link    = "{$url}/Articles/detail/?appid={$data['saas_appid']}&aid={$data['id']}";
        }
        return $link;
    }

    

    /**
     * 设置文章封面
     * @param mixed $value
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function setThumbAttr($value)
    {
        if (is_array($value)) {
            $value = UploadService::path($value);
        }
        return $value;
    }

    /**
     * 获取文章封面
     * @param mixed $value
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function getThumbAttr($value)
    {
        if ($value) {
            $value = UploadService::url($value);
        }
        return $value;
    }
}
