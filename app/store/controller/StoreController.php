<?php

namespace app\store\controller;

use app\common\builder\FormBuilder;
use app\common\BaseController;
use app\common\service\UploadService;
use app\store\validate\Store;
use support\Request;

/**
 * 租户信息管理
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-12
 */
class StoreController extends BaseController
{
    /**
     * 模型
     * @var \app\common\model\Store
     */
    public $model;

    /**
     * 构造函数
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public function __construct()
    {
        $this->model = new \app\common\model\Store;
    }

    /**
     * 修改
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public function edit(Request $request)
    {
        $store_id = $request->user['id'];
        $model = $this->model;
        $where = [
            ['id','=',$store_id],
        ];
        $model = $model->where($where)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() == 'POST') {
            $post = $request->post();
            // 数据验证
            hpValidate(Store::class, $post, 'edit');

            $post['logo'] = UploadService::path($post['logo']);
            if (empty($post['password'])) {
                unset($post['password']);
            }

            if (!$model->save($post)) {
                return parent::fail('保存失败');
            }
            return parent::success('保存成功');
        }
        $builder = new FormBuilder;
        $builder->setMethod('POST')
        ->addRow('title', 'input', '用户名称', '', [
            'col' => 12,
        ])
        ->addRow('password', 'input', '登录密码', '', [
            'col' => 12,
            'placeholder'=> '不填写则不修改',
        ])
        ->addComponent('logo', 'uploadify', '用户头像', '', [
            'props' => [
                'type'   => 'image',
                'format' => ['jpg', 'png', 'gif']
            ],
        ]);
        $builder->setData($model);
        $data = $builder->create();
        return parent::successRes($data);
    }
}