<?php

namespace app\admin\controller;

use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\admin\model\Store;
use app\common\enum\UploadifyAuthEnum;
use app\common\model\StoreApp;
use app\common\model\SystemConfig;
use app\common\model\SystemUpload;
use app\common\model\SystemUploadCate;
use app\admin\validate\Store as ValidateStore;
use app\common\BaseController;
use app\common\enum\PlatformTypes;
use Exception;
use loong\oauth\facade\Auth;
use support\Request;
use think\facade\Db;
use think\facade\Session;

/**
 * 站点管理
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-12
 */
class StoreController extends BaseController
{
    /**
     * 模型
     * @var Store
     */
    protected $model;

    /**
     * 构造函数
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new Store;
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
        /* $platformAssets = PlatformTypes::toArray();
        foreach ($platformAssets as $key => $value) {
            $platformAssets[$key]['title'] = $value['text'];
            $platformAssets[$key]['field'] = $value['value'];
        } */
        $builder = new ListBuilder;
        $data    = $builder
            ->addActionOptions('操作', [
                'width' => 380
            ])
            ->pageConfig()
            ->editConfig()
            ->rowConfig([
                'height' => 70
            ])
            ->addTopButton('add', '创建站点', [
                'type' => 'page',
                'api' => 'admin/Store/add',
                'path' => '/Store/add',
            ], [
                'title' => '创建站点',
            ], [
                'type' => 'primary'
            ])
            ->addRightButton(
                'toStore',
                '管理',
                [
                    'type' => 'link',
                    'api' => 'admin/Store/login',
                    'aliasParams' => [
                        'id' => 'store_id'
                    ],
                ],
                [],
                [
                    'type' => 'primary',
                    'icon' => 'CodeSandboxOutlined'
                ]
            )
            ->addRightButton('storeApp', '授权', [
                'type' => 'page',
                'api' => 'admin/StoreApp/index',
                'path' => '/StoreApp/index',
                'aliasParams' => [
                    'id' => 'store_id'
                ],
            ], [], [
                'type' => 'warning',
                'icon' => 'CompassOutlined'
            ])
            ->addRightButton('copyrightSet', '版权设置', [
                'group' => true,
                'type' => 'page',
                'api' => 'admin/Store/copyrightSet',
                'path' => '/Store/copyrightSet',
            ], [
                'title' => '站点版权设置',
            ], [
                'type' => 'primary',
                'icon' => 'EditOutlined'
            ])
            ->addRightButton('projects', '项目', [
                'type' => 'page',
                'api' => 'admin/StoreProject/index',
                'path' => '/StoreProject/index',
            ], [], [
                'type' => 'info',
                'icon' => 'DesktopOutlined'
            ])
            ->addRightButton('edit', '修改站点', [
                'group' => true,
                'type' => 'page',
                'api' => 'admin/Store/edit',
                'path' => '/Store/edit',
            ], [
                'title' => '修改站点',
            ], [
                'type' => 'primary',
                'icon' => 'EditOutlined'
            ])
            ->addRightButton('del', '删除站点', [
                'group' => true,
                'type' => 'confirm',
                'api' => 'admin/Store/del',
                'method' => 'delete',
            ], [
                'type' => 'error',
                'title' => '温馨提示',
                'content' => '是否将该站点放入回收站？',
            ], [
                'type' => 'danger',
                'icon' => 'RestOutlined'
            ])
            ->addScreen('keyword', 'input', '站点名称')
            ->addScreen('username', 'input', '账号')
            ->addColumn('title', '站点名称')
            ->addColumn('username', '账号')
            ->addColumnEle('logo', '图标', [
                'width' => 60,
                'params' => [
                    'type' => 'image',
                ],
            ])
            ->addColumnEle('status', '状态', [
                'width' => 100,
                'params' => [
                    'type' => 'switch',
                    'api' => '/admin/Store/rowEdit',
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
            /* ->addColumnEle('surplusNum', '站点资产：已创建/总数量', [
                'width' => 330,
                'params' => [
                    'type' => 'assets',
                    'resource' => $platformAssets,
                ]
            ]) */
            ->addColumnEle('is_uploadify', '附件库权限', [
                'width' => 150,
                'params' => [
                    'type' => 'tags',
                    'options' => UploadifyAuthEnum::dictOptions(),
                    'style' => [
                        '10' => [
                            'type' => 'danger',
                        ],
                        '20' => [
                            'type' => 'success',
                        ],
                    ],
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
        $limit = $request->get('limit', 10);
        $keyword = $request->get('keyword', '');
        $where   = [];
        if ($keyword) {
            $where[] = ['title', 'like', '%' . $keyword . '%'];
        }
        $username = $request->get('username', '');
        if ($username) {
            $where[] = ['username', 'like', '%' . $username . '%'];
        }
        if ($request->user['role']['is_system'] !== '20') {
            $where[] = ['admin_id', '=', $request->user['id']];
        }
        $model = $this->model;
        $data  = $model->where($where)
            ->order(['id' => 'desc'])
            ->paginate($limit)
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
        $validate = ValidateStore::class;
        if ($request->method() == 'POST') {
            $post           = $request->post();
            $post['status'] = '20';

            # 数据验证
            hpValidate($validate, $post, 'add');

            Db::startTrans();
            try {
                # 创建站点
                $model = $this->model;
                $post['admin_id'] = $request->user['id'];
                if (!$model->save($post)) {
                    throw new Exception('创建站点失败');
                }
                # 创建附件库分类
                $cateData        = [
                    'store_id'      => $model->id,
                    'title'         => '默认分类',
                    'dir_name'      => "store_{$model->id}",
                    'sort'          => 100,
                    'is_system'     => '20'
                ];
                $uploadCateModel = new SystemUploadCate;
                if (!$uploadCateModel->save($cateData)) {
                    throw new Exception('创建站点默认分类失败');
                }
                # 提交事务
                Db::commit();
            } catch (\Throwable $e) {
                Db::rollback();
                throw $e;
            }
            return $this->success('保存成功');
        }
        $data = $this->formView()->setMethod('POST')->create();
        return $this->successRes($data);
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
            hpValidate(ValidateStore::class, $post, 'edit');

            # 验证是否空密码
            if (empty($post['password']) && isset($post['password'])) {
                unset($post['password']);
            }
            # 关闭附件库权限
            if ($post['is_uploadify'] === '10') {
                $this->closeUploadifyAuth($id);
            }
            if (!$model->save($post)) {
                return parent::fail('保存失败');
            }
            return parent::success('保存成功');
        }
        $formData = $model->toArray();
        $data     = $this->formView()->setMethod('PUT')->setFormData($formData)->create();
        return parent::successRes($data);
    }

    /**
     * 关闭附件库权限
     * @param mixed $store_id
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private function closeUploadifyAuth($store_id)
    {
        $where = [
            'store_id' => $store_id
        ];
        $data  = StoreApp::where($where)->column('id');
        foreach ($data as $saas_appid) {
            $where = [
                'saas_appid' => $saas_appid,
                'group' => 'upload'
            ];
            $model = SystemConfig::where($where)->find();
            if ($model) {
                $configValue                 = $model->value;
                $configValue['upload_drive'] = 'aliyun';
                $model->value                = $configValue;
                if (!$model->save()) {
                    throw new Exception('关闭附件库权限失败');
                }
            }
        }
    }

    /**
     * 获取表单视图
     * @return FormBuilder
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private function formView()
    {
        $builder = new FormBuilder;
        $builder = $builder
            ->setMethod('PUT')
            ->addRow('username', 'input', '站点账号', '', [
                'col' => 12,
            ])
            ->addRow('password', 'input', '登录密码', '', [
                'col' => 12,
            ])
            ->addRow('title', 'input', '站点名称', '', [
                'col' => 12,
            ])
            ->addComponent('logo', 'uploadify', '站点图标', '', [
                'col' => 6,
                'props' => [
                    'suffix' => ['jpg', 'jpeg', 'png', 'gif']
                ],
            ])
            ->addRow('is_uploadify', 'radio', '附件库权限', '20', [
                'col' => 6,
                'options' => UploadifyAuthEnum::getOptions(),
            ])
            /* ->addRow('wechat', 'input', '公众号数量', '', [
                'col' => 12,
            ])
            ->addRow('mini_wechat', 'input', '微信小程序', '', [
                'col' => 12,
            ])
            ->addRow('douyin', 'input', '抖音小程序', '', [
                'col' => 12,
            ])
            ->addRow('h5', 'input', '网页应用', '', [
                'col' => 12,
            ])
            ->addRow('app', 'input', 'APP应用', '', [
                'col' => 12,
            ])
            ->addRow('other', 'input', '其他应用', '', [
                'col' => 12,
            ]) */
            ->addRow('remarks', 'textarea', '站点备注（可选）', '');
        return $builder;
    }

    /**
     * 版权设置
     * @param \support\Request $request
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function copyrightSet(Request $request)
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
        if ($request->method() === 'PUT') {
            $post = $request->post();
            if (!$model->save($post)) {
                return $this->fail('保存版权设置失败');
            }
            return $this->success('保存版权设置成功');
        }
        $formData = $model->toArray();
        $builder  = new FormBuilder;
        $data     = $builder
            ->setMethod('PUT')
            ->addRow('title', 'input', '站点名称', '', [
                'col' => [
                    'span' => 12
                ],
            ])
            ->addRow('copyright_service', 'input', '专属客服', '', [
                'placeholder' => '站点首页展示的专属客服，如不填写，则按照系统配置中的显示',
                'col' => [
                    'span' => 12
                ],
            ])
            ->addRow('copyright_tutorial', 'textarea', '系统教程', '', [
                'placeholder' => '站点首页展示的系统教程，如不填写，则按照系统配置中的显示',
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
        $id    = $request->post('id');
        $model = $this->model;

        # 开启事务
        Db::startTrans();
        try {
            # 查询站点信息
            $where = [
                'id' => $id
            ];
            $model = $model->where($where)->find();
            if (!$model) {
                throw new Exception('该数据不存在');
            }
            /* # 通用查询条件
            $where = [
                'store_id'      => $id
            ];
            # 删除站点旗下项目
            StoreApp::where($where)->delete();
            # 删除站点默认分类
            SystemUploadCate::where($where)->delete();
            # 删除站点所有附件
            SystemUpload::where($where)->delete();
            # 删除站点旗下配置项
            SystemConfig::where($where)->delete(); */
            # 删除站点
            if (!$model->delete()) {
                throw new Exception('删除站点失败');
            }
            Db::commit();
            return $this->success('删除成功');
        } catch (\Throwable $e) {
            Db::rollback();
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 登录站点平台
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public function login(Request $request)
    {
        $store_id = $request->get('store_id');
        if (!$store_id) {
            return parent::fail('该数据不存在');
        }
        // 查询数据
        $where      = [
            'id' => $store_id
        ];
        $adminModel = Store::where($where)->find();
        if (!$adminModel) {
            return parent::fail('登录错误');
        }

        // 构建令牌
        $data  = $adminModel->toArray();
        $token = Auth::encrypt($data);
        $token = urlencode($token);

        $url = "store/#/?token={$token}";
        return $this->successRes(['url' => $url]);
    }
}
