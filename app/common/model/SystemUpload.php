<?php

namespace app\common\model;

use app\common\Model;
use app\common\service\UploadService;

class SystemUpload extends Model
{
    protected $deleteTime = 'delete_time';

    # 模型输出字段
    protected $append = [
        'url',
        'size_format',
    ];

    /**
     * 关联附件分类
     * @return \think\model\relation\HasOne
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-30
     */
    public function category()
    {
        return $this->hasOne(SystemUploadCate::class, 'id', 'cid');
    }

    /**
     * 追加URL参数
     * @param mixed $value
     * @param mixed $data
     * @return string
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-30
     */
    protected function getUrlAttr($value, $data)
    {
        return UploadService::url((string) $data['path']);
    }
}
