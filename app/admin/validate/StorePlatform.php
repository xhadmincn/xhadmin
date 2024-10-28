<?php

namespace app\admin\validate;

use app\store\model\StorePlatformConfig;
use think\Validate;

class StorePlatform extends Validate
{
    protected $rule = [
        'store_id'      => 'require',
        'platform_type' => 'require|verifyPlatform',
        'title'         => 'require|verifyTitle',
        'logo'          => 'require',
    ];

    protected $message = [
        'store_id.require'      => '请选择所属租户',
        'platform_type.require' => '请选择平台类型',
        'title.require'         => '请输入平台名称',
        'logo.require'          => '请上传平台图标',
    ];

    /**
     * 添加场景验证
     * @return Validate
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public function sceneAdd()
    {
        return $this
            ->only([
                'store_id',
                'title',
                'logo',
            ]);
    }

    /**
     * 修改场景
     * @return Validate
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public function sceneEdit()
    {
        return $this
            ->only([
                'store_id',
                'title',
                'logo',
            ])
            ->remove('title', ['verifyTitle']);
    }

    /**
     * 验证平台名称
     * @param mixed $value
     * @param mixed $rule
     * @param mixed $data
     * @return bool|string
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-05
     */
    protected function verifyTitle($value, $rule, $data)
    {
        $where = [
            'config_value' => $data['title'],
            'store_id'     => $data['store_id']
        ];
        if (StorePlatformConfig::where($where)->count()) {
            return '该平台已存在';
        }
        return true;
    }

    /**
     * 验证平台数量
     * @param mixed $value
     * @param mixed $rule
     * @param mixed $data
     * @return bool|string
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-05
     */
    protected function verifyPlatform($value, $rule, $data)
    {
        $where = [
            'config_value' => $data['title'],
            'store_id'     => $data['store_id']
        ];
        if (StorePlatformConfig::where($where)->count()) {
            return '该平台已存在';
        }
        return true;
    }
}