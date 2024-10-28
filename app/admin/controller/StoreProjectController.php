<?php

namespace app\admin\controller;

use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\common\enum\PlatformTypesStyle;
use app\common\model\StoreApp;
use app\admin\validate\StoreApp as ValidateStoreApp;
use app\common\BaseController;
use app\common\enum\PlatformTypes;
use Exception;
use support\Request;
use think\facade\Db;

/**
 * 项目管理
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class StoreProjectController extends BaseController
{
    /**
     * 模型
     * @var StoreApp
     */
    protected $model;

    /**
     * 构造函数
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public function __construct()
    {
        $this->model = new StoreApp;
    }

    /**
     * 表格
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-30
     */
    public function indexGetTable(Request $request)
    {
        $builder = new ListBuilder;
        $data    = $builder
            ->addActionOptions('操作', [
                'width' => 200
            ])
            ->pageConfig()
            ->editConfig()
            ->rowConfig([
                'height' => 70
            ])
            ->addRightButton('edit', '修改', [
                'type' => 'page',
                'api' => 'admin/StoreProject/edit',
                'path' => '/StoreProject/edit',
            ], [
                'title' => '修改项目',
            ], [
                'type' => 'primary',
                'icon' => 'EditOutlined'
            ])
            ->addRightButton('del', '删除', [
                'type' => 'confirm',
                'api' => 'admin/StoreProject/del',
                'method' => 'delete',
            ], [
                'title' => '温馨提示',
                'content' => '该数据删除将不可恢复，请谨慎操作',
            ], [
                'type' => 'danger',
                'icon' => 'RestOutlined'
            ])
            ->addColumn('create_at', '创建时间', [
                'width' => 180,
            ])
            ->addColumn('title', '项目名称')
            ->addColumn('name', '应用标识')
            ->addColumnEle('logo', '图标', [
                'width' => 80,
                'params' => [
                    'type' => 'image',
                ],
            ])
            ->addColumnEle('status', '状态', [
                'width' => 100,
                'params' => [
                    'type' => 'switch',
                    'api' => '/admin/StoreProject/rowEdit',
                    'checked' => [
                        'text' => '正常',
                        'value' => '20'
                    ],
                    'unchecked' => [
                        'text' => '冻结',
                        'value' => '10'
                    ]
                ],
            ])
            ->addColumnEle('platform', '平台类型', [
                'width' => 150,
                'params' => [
                    'type' => 'tags',
                    'options' => PlatformTypes::dictOptions(),
                    'style' => PlatformTypesStyle::parseAlias('type')
                ],
            ])
            ->create();
        return parent::successRes($data);
    }

    /**
     * 列表
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-30
     */
    public function index(Request $request)
    {
        $store_id = $request->get('id', 0);
        $where      = [
            'store_id'      => $store_id
        ];
        $model      = $this->model;
        $data       = $model->where($where)
            ->order(['id' => 'desc'])
            ->paginate()
            ->each(function ($e) {
                return $e;
            })
            ->toArray();
        return parent::successRes($data);
    }

    /**
     * 添加
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-30
     */
    public function add(Request $request)
    {
        $validate = ValidateStoreApp::class;
        if ($request->method() == 'POST') {
            $post           = $request->post();
            $post['status'] = '20';

            // 数据验证
            hpValidate($validate, $post, 'edit');

            $model        = $this->model;
            if (!$model->save($post)) {
                return parent::fail('保存失败');
            }
            return parent::success('保存成功');
        }
        $builder = new FormBuilder;
        $data    = $builder
            ->setMethod('POST')
            ->addRow('title', 'input', '项目名称', '', [
                'col' => [
                    'span' => 12
                ],
            ])
            ->addComponent('logo', 'uploadify', '项目图标', '', [
                'col' => [
                    'span' => 12
                ],
            ])
            ->formValidate($validate)
            ->create();
        return parent::successRes($data);
    }

    /**
     * 修改
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-30
     */
    public function edit(Request $request)
    {
        $id    = $request->get('id');
        $where = [
            'id' => $id
        ];
        $model = $this->model;
        $model = $model->where($where)->find();
        if (!$model) {
            return parent::fail('该数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();

            // 数据验证
            hpValidate(ValidateStoreApp::class, $post, 'edit');

            if (!$model->save($post)) {
                return parent::fail('保存失败');
            }
            return parent::success('保存成功');
        }
        $formData                = $model->toArray();
        $builder                 = new FormBuilder;
        $data                    = $builder
            ->setMethod('PUT')
            ->addRow('title', 'input', '项目名称', '', [
                'col' => [
                    'span' => 12
                ],
            ])
            ->addComponent('logo', 'uploadify', '项目图标', '', [
                'col' => [
                    'span' => 12
                ],
            ])
            ->setFormData($formData)
            ->create();
        return parent::successRes($data);
    }

    /**
     * 删除
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-30
     */
    public function del(Request $request)
    {
        return $this->fail('该功能暂时禁用');
        $id    = $request->post('id');
        $model = $this->model;

        # 开启事务
        Db::startTrans();
        try {
            # 查询租户信息
            $where = [
                'id' => $id
            ];
            $model = $model->where($where)->find();
            if (!$model) {
                throw new Exception('该数据不存在');
            }
            Db::commit();
            return $this->success('删除成功');
        } catch (\Throwable $e) {
            Db::rollback();
            return $this->fail($e->getMessage());
        }
    }
}