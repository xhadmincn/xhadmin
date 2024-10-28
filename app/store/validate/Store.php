<?php

namespace app\store\validate;

use app\common\model\Store as storeModel;
use think\Validate;

class Store extends Validate
{
    protected $rule =   [
        'title'             => 'require|verifyTitle',
        'contact'           => 'require',
        'mobile'            => 'require|mobile',
        'logo'              => 'require',
    ];

    protected $message  =   [
        'title.require'     => '情输入用户名称',
        'contact.require'   => '请输入用户姓名',
        'mobile.require'    => '请输入联系手机',
        'mobile.mobile'     => '请输入正确的联系手机',
        'logo.require'      => '请上传租户图标',
    ];
    protected $scene = [
        'edit'  =>  ['title','logo'],
    ];

    /**
     * 验证租户名称
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-12
     * @param  string $value
     * @return bool|string
     */
    protected function verifyTitle($value)
    {
        $store_id = request()->user['id'];
        $where = [
            ['title','=',$value],
            ['id','<>',$store_id]
        ];
        if (storeModel::where($where)->count()) {
            return '该租户名称已存在';
        }
        return true;
    }
}
