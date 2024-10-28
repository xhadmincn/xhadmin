<?php

namespace app\common\model;

use app\common\enum\PlatformTypes;
use app\common\Model;
use app\common\service\UploadService;
use think\facade\Db;
use think\model\concern\SoftDelete;

/**
 * 应用模型
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-05-03
 */
class StoreApp extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    # 设置json类型字段
    protected $json = ['platform'];
    # 设置JSON字段的类型
    protected $jsonAssoc = true;

    # 追加字段显示
    protected $append = [
        'platform_text'
    ];

    /**
     * 一对一关联租户
     * @return \think\model\relation\HasOne
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-02
     */
    public function store()
    {
        return $this->hasOne(Store::class, 'id', 'store_id');
    }

    /**
     * 搜索器：平台类型
     * @param mixed $query
     * @param mixed $value
     * @param mixed $data
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function searchPlatformAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('platform', 'like', "%\"{$value}%");
        }
    }

    /**
     * 设置头像储存
     * @param mixed $value
     * @return array|string
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    protected function setLogoAttr($value)
    {
        return $value ? UploadService::path($value) : '';
    }

    /**
     * 解析URL
     * @param mixed $value
     * @return string
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    protected function getLogoAttr($value)
    {
        return $value ? UploadService::url((string) $value) : '';
    }

    /**
     * 返回JSON格式的平台类型
     * @param mixed $value
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected function getPlatformAttr($value, $data)
    {
        if (empty($value)) {
            $platform = Db::name('store_app')
                ->where(['id' => $data['id']])
                ->value('platform');
            return [$platform];
        }
        return $value;
    }

    /**
     * 设置平台类型JSON储存
     * @param mixed $value
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected function setPlatformAttr($value)
    {
        if (is_array($value)) {
            $value = array_filter($value);
        }
        if (empty($value)) {
            return ['other'];
        }
        if (!is_array($value)) {
            return [$value];
        }
        return $value;
    }

    /**
     * 获取平台类型文本
     * @param mixed $value
     * @param mixed $data
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected function getPlatformTextAttr($value, $data)
    {
        $platform  = '';
        if (isset($data['platform'])) {
            $platform = $data['platform'];
        }
        if (empty($platform)) {
            return '请重新编辑项目保存';
        }
        $dataText = [];
        foreach ($platform as $item) {
            $dataText[] = PlatformTypes::getText($item);
        }
        return implode(',', $dataText);
    }

    /**
     * 获取select组件值
     * @param array $where
     * @param string|array $field
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public static function selectOptions(array $where = [], string|array $field = '*')
    {
        $field = [
            'title as label',
            'id as value'
        ];
        $data  = StoreApp::where($where)
            ->field($field)
            ->select()
            ->each(function ($value) {
                $value->label = "应用：{$value['label']}";
                return $value;
            })
            ->toArray();
        return $data;
    }
}
