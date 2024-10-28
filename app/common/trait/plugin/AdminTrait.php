<?php

namespace app\common\trait\plugin;

use app\admin\validate\SystemAdmin;
use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\common\enum\StatusEnum;
use app\common\model\plugin\PluginAdmin;
use app\common\service\UploadService;
use app\common\utils\Json;
use support\Request;
use think\App;
use think\facade\Session;

/**
 * 应用管理员
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
trait AdminTrait
{
    // 使用JSON工具类
    use Json;

    /**
     * 应用ID（null则获取系统配置）
     * @var int|null
     */
    protected $saas_appid = null;

    /**
     * 插件路径前缀
     * @var string|null
     */
    protected $pluginPrefix = null;

    /**
     * 应用管理员模型
     * @var PluginAdmin
     */
    protected $model = null;

    /**
     * 构造函数
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new PluginAdmin;
    }

    /**
     * 获取表格
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function indexGetTable(Request $request)
    {
        $builder = new ListBuilder;
        $data = $builder
            ->addActionOptions('操作', [
                'width'         => 180
            ])
            ->pageConfig()
            ->addTopButton('add', '添加', [
                'api'           => $this->pluginPrefix . '/admin/Admin/add',
                'path'          => '/Admin/add',
            ], [], [
                'type'          => 'primary'
            ])
            ->addRightButton('edit', '修改', [
                'api'           => $this->pluginPrefix . '/admin/Admin/edit',
                'path'          => '/Admin/edit',
            ], [], [
                'type'          => 'primary',
            ])
            ->addRightButton('del', '删除', [
                'type'          => 'confirm',
                'api'           => $this->pluginPrefix . '/admin/Admin/del',
                'method'        => 'delete',
            ], [
                'type'          => 'error',
                'title'         => '温馨提示',
                'content'       => '是否确认删除该数据',
            ], [
                'type'          => 'danger',
            ])
            ->addColumn('username', '登录账号')
            ->addColumn('nickname', '用户昵称')
            ->addColumnEle('headimg', '用户头像', [
                'params'        => [
                    'type'      => 'image',
                ],
            ])
            ->addColumn('role.title', '所属部门')
            ->addColumn('last_login_ip', '最近登录IP')
            ->addColumn('last_login_time', '最近登录时间')
            ->addColumnEle('status', '当前状态', [
                'width'             => 90,
                'params'            => [
                    'type'          => 'tags',
                    'options'       => StatusEnum::dictOptions(),
                    'style'         => [
                        '10' => [
                            'type'  => 'danger',
                        ],
                        '20' => [
                            'type'  => 'success',
                        ],
                    ],
                ],
            ])
            ->create();
        return $this->successRes($data);
    }

    /**
     * 列表
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function index(Request $request)
    {
        $where    = [
            'saas_appid'    => $this->saas_appid,
            'is_system'     => '10'
        ];
        $data = $this->model->with(['role'])
            ->where($where)
            ->paginate()
            ->toArray();
        return $this->successRes($data);
    }

    /**
     * 添加
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function add(Request $request)
    {
        $admin_id = $request->user['id'];
        if ($request->method() == 'POST') {
            $post = $request->post();
            $post['pid'] = $admin_id;
            $post['saas_appid'] = $this->saas_appid;

            # 数据验证
            hpValidate(SystemAdmin::class, $post, 'add');

            # 验证是否已存在
            $where = [
                'username'      => $post['username']
            ];
            if ($this->model->where($where)->count()) {
                return $this->fail('该登录账号已存在');
            }
            if (!empty($post['headimg'])) {
                $post['headimg'] = current($post['headimg']);
            }
            $model = $this->model;
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $builder = new FormBuilder;
        $data = $builder
            ->setMethod('POST')
            ->addRow('role_id', 'select', '所属部门', '', [
                'col'       => [
                    'span'  => 12
                ],
                'options'   => $this->model->selectRolesOptions($this->saas_appid)
            ])
            ->addRow('status', 'radio', '用户状态', '10', [
                'col'       => [
                    'span'  => 12
                ],
                'options'   => StatusEnum::getOptions()
            ])
            ->addRow('username', 'input', '登录账号', '', [
                'col'       => [
                    'span'  => 12
                ],
            ])
            ->addRow('password', 'input', '登录密码', '', [
                'col'       => [
                    'span'  => 12
                ],
                'placeholder'   => '不填写，则不修改密码',
            ])
            ->addRow('nickname', 'input', '用户昵称', '', [
                'col'       => [
                    'span'  => 12
                ],
            ])
            ->addComponent('headimg', 'uploadify', '用户头像', '', [
                'col'           => [
                    'span'      => 12
                ],
                'props'         => [
                    'format'    => ['jpg', 'png', 'gif']
                ],
            ])
            ->create();
        return parent::successRes($data);
    }

    /**
     * 修改数据
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function edit(Request $request)
    {
        $admin_id = $request->user['id'];
        $id = $request->get('id');
        $where    = [
            'id'            => $id,
            'pid'           => $admin_id,
            'saas_appid'    => $this->saas_appid,
        ];
        $model = $this->model->where($where)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();

            // 数据验证
            hpValidate(SystemAdmin::class, $post, 'edit');

            // 空密码，不修改
            if (empty($post['password'])) {
                unset($post['password']);
            }
            if (!empty($post['headimg'])) {
                $post['headimg'] = current($post['headimg']);
            }
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $builder = new FormBuilder;
        $data = $builder
            ->setMethod('PUT')
            ->addRow('role_id', 'select', '所属角色', '', [
                'col'       => [
                    'span'  => 12
                ],
                'options'   => $this->model->selectRolesOptions($this->saas_appid)
            ])
            ->addRow('status', 'radio', '用户状态', '10', [
                'col'       => [
                    'span'  => 12
                ],
                'options'   => StatusEnum::getOptions()
            ])
            ->addRow('username', 'input', '登录账号', '', [
                'col'       => [
                    'span'  => 12
                ],
            ])
            ->addRow('password', 'input', '登录密码', '', [
                'col'       => [
                    'span'  => 12
                ],
                'placeholder'   => '不填写，则不修改密码',
            ])
            ->addRow('nickname', 'input', '用户昵称', '', [
                'col'       => [
                    'span'  => 12
                ],
            ])
            ->addComponent('headimg', 'uploadify', '用户头像', '', [
                'col'           => [
                    'span'      => 12
                ],
                'props'         => [
                    'format' => ['jpg', 'png', 'gif']
                ],
            ])
            ->setData($model)
            ->create();
        return parent::successRes($data);
    }

    /**
     * 修改自身数据
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-30
     */
    public function editSelf(Request $request)
    {
        $admin_id = $request->user['id'];
        $where    = [
            ['id', '=', $admin_id]
        ];
        $model    = $this->model->where($where)->find();
        if (!$model) {
            return parent::fail('该数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();

            // 数据验证
            hpValidate(SystemAdmin::class, $post, 'editSelf');

            // 空密码，不修改
            if (empty($post['password'])) {
                unset($post['password']);
            }
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            $adminModel = $this->model->with(['role'])->where($where)->find();
            // 更新session
            Session::set($request->plugin, $adminModel);

            return $this->success('保存成功');
        }
        $builder = new FormBuilder;
        $data    = $builder
            ->setMethod('PUT')
            ->addRow('username', 'input', '登录账号', '', [
                'col' => [
                    'span' => 12
                ],
            ])
            ->addRow('password', 'input', '登录密码', '', [
                'col'         => [
                    'span' => 12
                ],
                'placeholder' => '不填写，则不修改密码',
            ])
            ->addRow('nickname', 'input', '用户昵称', '', [
                'col' => [
                    'span' => 12
                ],
            ])
            /* ->addComponent('headimg', 'uploadify', '用户头像', '', [
                'col'   => [
                    'span' => 12
                ],
                'props' => [
                    'ext'  => ['jpg', 'png', 'gif']
                ],
            ]) */
            ->setData($model)
            ->create();
        return $this->successRes($data);
    }

    /**
     * 删除数据
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function del(Request $request)
    {
        $id = $request->post('id');
        if (!$this->model->destroy($id)) {
            return $this->fail('删除失败');
        }
        return $this->success('删除成功');
    }
}
