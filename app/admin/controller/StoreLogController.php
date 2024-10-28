<?php

namespace app\admin\controller;

use app\common\builder\ListBuilder;
use app\admin\model\Store;
use app\common\BaseController;
use support\Request;

/**
 * 租户日志
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-12
 */
class StoreLogController extends BaseController
{
    /**
     * 表格列
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-12
     * @param  Request $request
     * @return void
     */
    public function indexGetTable(Request $request)
    {
        $builder = new ListBuilder;
        $data = $builder
            ->pageConfig()
            ->addColumn('title', '商户名称')
            ->addColumn('username', '商户账号')
            ->addColumnEle('logo', '商户图标', [
                'params'        => [
                    'type'      => 'image',
                ],
            ])
            ->addColumnEle('status', '商户状态', [
                'width'         => 100,
                'params'        => [
                    'type'      => 'tags',
                    'options'   => ['冻结', '正常'],
                    'props'     => [
                        [
                            'type'  => 'danger'
                        ],
                        [
                            'type'  => 'success'
                        ],
                    ],
                ],
            ])
            ->addColumn('platform_wechat', '微信公众号', [
                'width'             => 100
            ])
            ->addColumn('platform_min_wechat', '微信小程序', [
                'width'             => 100
            ])
            ->addColumn('platform_douyin', '抖音应用', [
                'width'             => 100
            ])
            ->addColumn('platform_app', 'APP应用', [
                'width'             => 100
            ])
            ->addColumn('platform_h5', '网页应用', [
                'width'             => 100
            ])
            ->addColumn('platform_other', '其他应用', [
                'width'             => 100
            ])
            ->create();
        return parent::successRes($data);
    }

    /**
     * 列表
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-12
     * @param  Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $data = Store::order(['id' => 'desc'])
            ->paginate()
            ->toArray();
        return parent::successRes($data);
    }
}
