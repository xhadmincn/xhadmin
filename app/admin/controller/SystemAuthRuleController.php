<?php

namespace app\admin\controller;

use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\common\enum\AuthRuleRuleTypeStyle;
use app\common\enum\ShowStatus;
use app\common\enum\ShowStatusStyle;
use app\common\enum\YesNoEum;
use app\common\enum\YesNoEumStyle;
use app\common\service\AuthRuleService;
use app\common\model\SystemAuthRule;
use app\admin\validate\SystemAuthRule as ValidateSystemAuthRule;
use app\common\BaseController;
use app\common\enum\AuthRuleMethods;
use app\common\enum\AuthRuleRuleType;
use Exception;
use support\Request;
use FormBuilder\Factory\Elm;
use think\facade\Db;

/**
 * 权限菜单
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-07
 */
class SystemAuthRuleController extends BaseController
{
    /**
     * 表格
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function indexGetTable(Request $request)
    {
        $builder = new ListBuilder;
        $data    = $builder
            ->addActionOptions('操作', [
                'width' => 180
            ])
            ->treeConfig([
                'rowField' => 'id',
            ])
            ->addTopButton('add', '添加', [
                'api' => 'admin/SystemAuthRule/add',
                'path' => '/SystemAuthRule/add'
            ], [], [
                'type' => 'primary'
            ])
            ->addRightButton('edit', '修改', [
                'api' => 'admin/SystemAuthRule/edit',
                'path' => '/SystemAuthRule/edit'
            ], [], [
                'type' => 'primary',
            ])
            ->addRightButton('del', '删除', [
                'type'      => 'confirm',
                'api'       => 'admin/SystemAuthRule/del',
                'method'    => 'delete',
            ], [
                'type'      => 'error',
                'title'     => '温馨提示',
                'content'   => '是否确认删除该数据',
            ], [
                'type' => 'danger',
            ])
            ->addColumn('path_text', '权限地址', [
                'treeNode' => true
            ])
            ->addColumn('title', '权限名称')
            ->addColumnEle('show', '是否显示', [
                'width' => 100,
                'params' => [
                    'type' => 'tags',
                    'options' => ShowStatus::dictOptions(),
                    'style' => ShowStatusStyle::parseAlias('type'),
                ],
            ])
            ->addColumnEle('is_api', '是否接口', [
                'width' => 100,
                'params' => [
                    'type' => 'tags',
                    'options' => YesNoEum::dictOptions(),
                    'style' => YesNoEumStyle::parseAlias('type'),
                ],
            ])
            ->addColumnEle('component', '组件类型', [
                'width' => 120,
                'params' => [
                    'type' => 'tags',
                    'options' => AuthRuleRuleType::dictOptions(),
                    'style' => AuthRuleRuleTypeStyle::parseAlias('type', false),
                ],
            ])
            ->addColumn('method', '请求类型', [
                'width' => 180
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
     * @DateTime 2023-04-29
     */
    public function index(Request $request)
    {
        $list = SystemAuthRule::order(['sort' => 'asc', 'id' => 'asc'])
            ->select()
            ->toArray();
        return parent::successRes($list);
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
        if ($request->method() == 'POST') {
            $post  = $request->post();
            $model = new SystemAuthRule;

            // 数据验证
            hpValidate(ValidateSystemAuthRule::class, $post, 'add');

            // 额外验证
            if ($post['component'] !== 'remote/index') {
                if (!isset($post['path']) || !$post['path'] && $post['is_api'] === '1') {
                    return parent::fail('请输入权限路由');
                }
                if (!isset($post['method']) || empty($post['method'])) {
                    return parent::fail('至少选择一个请求类型');
                }
            }
            if (!isset($post['method'])) {
                $post['method'] = ['GET'];
            }

            if (!$model->save($post)) {
                return parent::fail('添加失败');
            }

            return parent::success('添加成功');
        }
        $view = $this->formView()
            ->setMethod('POST')
            ->create();
        return parent::successRes($view);
    }

    /**
     * 添加资源菜单
     * @param \support\Request $request
     * @copyright 贵州猿创科技有限公司
     * @return \support\Response
     * @author John
     */
    public function addResource(Request $request)
    {
        if ($request->method() === 'POST') {
            $post = $request->post();
            # 数据验证
            hpValidate(ValidateSystemAuthRule::class, $post, 'add');
            # 额外验证
            if (strpos($post['path'], '/') !== false) {
                throw new Exception('权限路由仅需输入控制器名');
            }
            # 菜单图标
            if (isset($post['icon']) && is_array($post['icon'])) {
                $post['icon'] = $post['icon']['icon'];
            }
            Db::startTrans();
            try {
                $mainData = [
                    'title'         => $post['title'],
                    'pid'           => $post['pid'],
                    'component'     => 'table/index',
                    'is_api'        => '20',
                    'show'          => $post['show'],
                    'sort'          => $post['sort'],
                    'icon'          => $post['icon'],
                    'path'          => "{$post['path']}/index",
                    'method'        => $post['method'],
                    'auth_params'   => empty($post['auth_params']) ? '' : $post['auth_params'],
                ];
                $model = new SystemAuthRule;
                $model->save($mainData);
                $Methods = [
                    [
                        'title'     => '添加',
                        'component' => 'form/index',
                        'field'     => 'add',
                        'method'    => ['GET','POST'],
                    ],
                    [
                        'title'     => '编辑',
                        'component' => 'form/index',
                        'field'     => 'edit',
                        'method'    => ['GET','PUT'],
                    ],
                    [
                        'title'     => '删除',
                        'component' => 'none/index',
                        'field'     => 'del',
                        'method'    => ['GET','DELETE'],
                    ],
                    [
                        'title'     => '表格列',
                        'component' => 'none/index',
                        'field'     => 'indexTable',
                        'method'    => ['GET'],
                    ],
                ];
                foreach ($Methods as $value) {
                    $item = [
                        'title'         => $post['title'] . ' - ' . $value['title'],
                        'pid'           => $model->id,
                        'component'     => $value['component'],
                        'is_api'        => '20',
                        'show'          => '10',
                        'sort'          => 100,
                        'icon'          => '',
                        'path'          => "{$post['path']}/{$value['field']}",
                        'method'        => $value['method'],
                        'auth_params'   => '',
                    ];
                    $childModel = new SystemAuthRule;
                    $childModel->save($item);
                }
                Db::commit();
            } catch (\Throwable $e) {
                Db::rollback();
                throw $e;
            }
            return $this->success('添加资源菜单成功');
        }
        $builder = new FormBuilder;
        $builder->addRow('title', 'input', '菜单名称', '', [
            'col' => [
                'span' => 12
            ],
        ])
            ->addRow('pid', 'cascader', '父级菜单', [], [
                'props' => [
                    'props' => [
                        'checkStrictly' => true,
                    ],
                ],
                'options' => AuthRuleService::getCascaderOptions(),
                'placeholder' => '如不选择则是顶级菜单',
                'col' => [
                    'span' => 12
                ],
            ])
            ->addComponent('component', 'el-alert', '菜单类型', '', [
                'col' => [
                    'span' => 12
                ],
                'props' => [
                    'type'          => 'success',
                    'closable'      => false,
                    'title'         => "表格组件",
                ],
            ])
            ->addRow('path', 'input', '权限路由', '', [
                'placeholder' => '示例：控制器/方法',
                'col' => [
                    'span' => 12
                ],
            ])
            ->addRow('auth_params', 'input', '附带参数', '', [
                'placeholder' => '附带地址栏参数（选填），格式：name=楚羽幽&sex=男',
            ])
            ->addRow('show', 'radio', '显示隐藏', '10', [
                'options' => ShowStatus::getOptions(),
                'col' => [
                    'span' => 12
                ],
            ])
            ->addRow('method', 'checkbox', '请求类型', ['GET'], [
                'options' => AuthRuleMethods::getOptions(),
                'col' => [
                    'span' => 12
                ],
            ])
            ->addComponent('icon', 'icons', '菜单图标', '', [
                'col' => [
                    'span' => 12
                ],
            ])
            ->addRow('sort', 'input', '菜单排序', '0', [
                'col' => [
                    'span' => 12
                ],
            ])
            ->setMethod('POST')
            ->addComponent('tip', 'el-alert', '温馨提示', '', [
                'props' => [
                    'type'          => 'error',
                    'closable'      => false,
                    'title'         => "资源菜单是指，权限路由仅需填写控制器名，则自动创建以下菜单 ---- 列表：index，表格列indexTable，添加：add，编辑：edit，删除：del",
                ],
            ]);
        $view = $builder->create();
        return $this->successRes($view);
    }

    /**
     * 修改
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function edit(Request $request)
    {
        $id    = $request->get('id');
        $where = [
            ['id', '=', $id],
        ];
        $model = SystemAuthRule::where($where)->find();
        if (!$model) {
            return parent::fail('该数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();

            # 数据验证
            hpValidate(ValidateSystemAuthRule::class, $post, 'edit');

            # 额外验证
            if ($post['component'] !== 'remote/index') {
                if (!isset($post['path']) || !$post['path'] && $post['is_api'] === '1') {
                    return parent::fail('请输入权限路由');
                }
                if (!isset($post['method']) || empty($post['method'])) {
                    return parent::fail('至少选择一个请求类型');
                }
            }
            # 菜单图标
            if (isset($post['icon']) && is_array($post['icon'])) {
                $post['icon'] = $post['icon']['icon'];
            }

            if (!$model->save($post)) {
                return parent::fail('修改失败');
            }
            return parent::success('修改成功');
        }
        $model->pid = [$model['pid']];
        $view       = $this->formView()
            ->setMethod('PUT')
            ->setData($model)
            ->create();
        return $this->successRes($view);
    }

    /**
     * 表单视图
     * @return \app\common\builder\FormBuilder
     * @author YC科技
     */
    private function formView(): FormBuilder
    {
        $builder = new FormBuilder;
        $builder->addRow('title', 'input', '菜单名称', '', [
            'col' => [
                'span' => 12
            ],
        ])
            ->addRow('pid', 'cascader', '父级菜单', [], [
                'props' => [
                    'props' => [
                        'checkStrictly' => true,
                    ],
                ],
                'options' => AuthRuleService::getCascaderOptions(),
                'placeholder' => '如不选择则是顶级菜单',
                'col' => [
                    'span' => 12
                ],
            ])
            ->addRow('component', 'select', '菜单类型', 'none/index', [
                'options' => AuthRuleRuleType::getOptions(),
                'col' => [
                    'span' => 12
                ],
                // 使用联动组件
                'control' => [
                    [
                        'value' => 'remote/index',
                        'where' => '==',
                        'rule' => [
                            Elm::input('auth_params', '远程组件')
                                ->props([
                                    'placeholder' => '示例：remote/index，则会自动加载 http://www.xxx.com/remote/index.vue 文件作为组件渲染',
                                ])
                                ->col([
                                    'span' => 12
                                ]),
                        ],
                    ],
                    [
                        'value' => ['', 'table/index', 'form/index'],
                        'where' => 'in',
                        'rule' => [
                            Elm::input('auth_params', '附带参数')
                                ->props([
                                    'placeholder' => '附带地址栏参数（选填），格式：name=楚羽幽&sex=男'
                                ])
                                ->col([
                                    'span' => 12
                                ]),
                        ],
                    ],
                ],
            ])
            ->addRow('is_api', 'radio', '是否接口', '10', [
                'options' => YesNoEum::getOptions(),
                'col' => [
                    'span' => 12
                ],
            ])
            ->addRow('path', 'input', '权限路由', '', [
                'placeholder' => '示例：控制器/方法',
                'col' => [
                    'span' => 12
                ],
            ])
            ->addRow('show', 'radio', '显示隐藏', '10', [
                'options' => ShowStatus::getOptions(),
                'col' => [
                    'span' => 12
                ],
            ])
            ->addRow('method', 'checkbox', '请求类型', ['GET'], [
                'options' => AuthRuleMethods::getOptions(),
                'col' => [
                    'span' => 12
                ],
            ])
            ->addComponent('icon', 'icons', '菜单图标', '', [
                'col' => [
                    'span' => 12
                ],
            ])
            ->addRow('sort', 'input', '菜单排序', '0', [
                'col' => [
                    'span' => 12
                ],
            ]);
        return $builder;
    }

    /**
     * 删除
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function del(Request $request)
    {
        $id    = $request->post('id');
        $where = [
            ['id', '=', $id]
        ];
        $model = SystemAuthRule::where($where)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($model->is_system === '20') {
            return $this->fail('系统级权限规则，禁止删除');
        }
        if (!$model->delete()) {
            return $this->fail('删除失败');
        }
        return parent::success('删除成功');
    }
}