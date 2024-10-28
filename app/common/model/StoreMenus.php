<?php

namespace app\common\model;

use app\common\Model;

/**
 * 租户菜单
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-12
 */
class StoreMenus extends Model
{
    // 设置JSON字段转换
    protected $json = ['method'];
    
    // 设置JSON数据返回数组
    protected $jsonAssoc = true;

    // 模型输出
    protected $append = [
        'path_text',
    ];

    /**
     * 修改器-检测父级菜单是否数组
     * @param mixed $value
     * @return mixed
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public function setPidAttr($value)
    {
        return is_array($value) ? end($value) : $value;
    }

    /**
     * 修改器-Path路径首字母转大写
     * @param mixed $value
     * @return string
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public function setPathAttr($value)
    {
        return ucfirst((string) $value);
    }

    /**
     * 获取完整的请求地址
     * @param mixed $value
     * @param mixed $data
     * @return string
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    protected function getPathTextAttr($value, $data)
    {
        return "store/{$data['path']}";
    }
}
