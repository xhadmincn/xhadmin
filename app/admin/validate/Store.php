<?php

namespace app\admin\validate;

use app\admin\model\Store as ModelStore;
use think\Validate;

class Store extends Validate
{
    protected $rule =   [
        'username'          => 'require|mobile|verifyUsername',
        'password'          => 'require',
        'title'             => 'require',
        'logo'              => 'require',
        /* 'wechat'            => 'require|number',
        'mini_wechat'       => 'require|number',
        'douyin'            => 'require|number',
        'h5'                => 'require|number',
        'app'               => 'require|number',
        'other'             => 'require|number', */
    ];

    protected $message  =   [
        'username.require'          => '请输入租户账号',
        'username.mobile'           => '租户账号必须是手机号',
        'password.require'          => '请输入租户密码',
        'title.require'             => '请输入租户名称',
        'logo.require'              => '请上传租户图标',
        'wechat.require'            => '请输入公众号数量',
        'wechat.number'             => '公众号数量必须是数字',
        'mini_wechat.require'       => '请输入微信小程序数量',
        'mini_wechat.number'        => '微信小程序必须是数字',
        'douyin.require'            => '请输入抖音小程序数量',
        'douyin.number'             => '抖音小程序必须是数字',
        'h5.require'                => '请输入网页应用数量',
        'h5.number'                 => '网页应用必须是数字',
        'app.require'               => '请输入APP数量',
        'app.number'                => 'APP必须是数字',
        'other.require'             => '请输入其他应用数量',
        'other.number'              => '其他应用必须是数字',
    ];

    /**
     * 登录场景
     * @return Store
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function sceneLogin()
    {
        return $this
            ->only([
                'username',
                'password',
            ])
            ->remove('username', ['verifyAdd']);
    }

    /**
     * 添加场景验证
     * @return Store
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function sceneAdd()
    {
        return $this
            ->only([
                'username',
                'password',
                'title',
                'expire_time',
                'contact',
                'mobile',
                'logo',
                'wechat',
                'mini_wechat',
                'douyin',
                'h5',
                'app',
                'other',
            ]);
    }

    /**
     * 修改场景
     * @return Store
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function sceneEdit()
    {
        return $this
            ->only([
                'username',
                'title',
                'expire_time',
                'contact',
                'mobile',
                'logo',
                'wechat',
                'mini_wechat',
                'douyin',
                'h5',
                'app',
                'other',
            ])
            ->remove('username', ['verifyUsername'])
            ->remove('title', ['verifyTitle']);
    }

    /**
     * 验证租户账号
     * @param mixed $value
     * @return bool|string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected function verifyUsername($value)
    {
        $where = [
            'username'  => $value
        ];
        if (ModelStore::where($where)->count()) {
            return '该账号已存在';
        }
        return true;
    }
}
