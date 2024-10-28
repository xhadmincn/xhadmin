<?php
namespace app\common\trait\plugin;

use app\admin\validate\SystemAuthRule;
use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\common\enum\AuthRuleRuleType;
use app\common\enum\AuthRuleRuleTypeStyle;
use app\common\enum\ShowStatus;
use app\common\enum\ShowStatusStyle;
use app\common\enum\YesNoEum;
use app\common\enum\YesNoEumStyle;
use app\common\manager\PluginMgr;
use app\common\utils\Data;
use app\common\utils\Json;
use FormBuilder\Factory\Elm;
use app\common\enum\AuthRuleMethods;
use support\Request;

/**
 * 权限菜单
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
trait MenusTrait
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
     * 获取菜单表格
     * @param \support\Request $request
     * @return mixed
     * @author John
     */
    public function indexGetTable(Request $request)
    {
        $builder = new ListBuilder;
        $data    = $builder
            ->addActionOptions('操作', [
                'width' => 180
            ])
            ->editConfig()
            ->treeConfig([
                'rowField' => 'id',
            ])
            ->addTopButton('add', '添加', [
                'api' => $this->pluginPrefix . '/admin/Menus/add',
                'path' => '/Menus/add'
            ], [], [
                'type' => 'primary'
            ])
            ->addRightButton('edit', '修改', [
                'api' => $this->pluginPrefix . '/admin/Menus/edit',
                'path' => '/Menus/edit'
            ], [], [
                'type' => 'primary',
            ])
            ->addRightButton('del', '删除', [
                'type' => 'confirm',
                'api' => $this->pluginPrefix . '/admin/Menus/del',
                'method' => 'delete',
            ], [
                'type' => 'error',
                'title' => '温馨提示',
                'content' => '是否确认删除该数据',
            ], [
                'type' => 'danger',
            ])
            ->addColumn('path', '权限地址', [
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
            ->addColumnEdit('sort', '排序', [
                'width' => 100,
                'params' => [
                    'type' => 'input',
                    'api' => $this->pluginPrefix . '/admin/Menus/rowEdit',
                    'min' => 0,
                ],
            ])
            ->create();
        return $this->successRes($data);
    }

    /**
     * 获取列表
     * @param \support\Request $request
     * @return mixed
     * @author John
     */
    public function index(Request $request)
    {
        $data = PluginMgr::getMenuList($request->plugin);
        $data = list_sort_by($data, 'sort', 'asc');
        return $this->successRes($data);
    }

    /**
     * 编辑行数据
     * @param \support\Request $request
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function rowEdit(Request $request)
    {
        # 需要修改的ID
        $id = $request->post('id');
        # 查询键
        $keyField = $request->post('keyField');
        # 修改键
        $field = $request->post('field');
        # 修改值
        $value = $request->post('value');
        # 获取列表
        $data = PluginMgr::getMenuList($request->plugin);
        # 查询数据
        $arrayIndex = array_search($id, array_column($data, $keyField));
        # 检测并判断数据
        $item = isset($data[$arrayIndex]) ? $data[$arrayIndex] : [];
        if (empty($item)) {
            return $this->fail('数据不存在');
        }
        $data[$arrayIndex][$field] = $value;
        # 保存菜单数据
        $this->saveData($data);
        return $this->success('修改成功');
    }

    /**
     * 添加菜单
     * @param \support\Request $request
     * @return mixed
     * @author John
     */
    public function add(Request $request)
    {
        if ($request->isPost()) {
            $post            = $request->post();
            $pluginMenusPath = root_path() . "plugin/{$request->plugin}/menus.json";
            if (!file_exists($pluginMenusPath)) {
                return $this->fail('插件菜单文件不存在');
            }
            // 数据验证
            hpValidate(SystemAuthRule::class, $post, 'add');
            // 额外验证
            if ($post['component'] !== 'remote/index') {
                if (!isset($post['path']) || !$post['path'] && $post['is_api'] === '20') {
                    return $this->fail('请输入权限路由');
                }
                if (!isset($post['method']) || empty($post['method'])) {
                    return $this->fail('至少选择一个请求类型');
                }
            }
            # 检测菜单路由不存在斜杠
            if (strpos($post['path'], '/') === false) {
                return $this->fail('权限路由格式错误');
            }
            if (!isset($post['method'])) {
                $post['method'] = ['GET'];
            }
            $menuData = $post;
            # 处理父级菜单
            if (is_array($menuData['pid'])) {
                $menuData['pid'] = end($menuData['pid']);
            }
            # 路由地址首字母转大写
            $menuData['path']       = ucfirst($menuData['path']);
            $data                   = PluginMgr::getMenuList($request->plugin);
            $data                   = list_sort_by($data, 'id', 'asc');
            $menuEnd                = end($data);
            $menuId                 = $menuEnd['id'] + 1;
            $menuData['icon']       = isset($menuData['icon']['icon']) ? $menuData['icon']['icon'] : '';
            $menuData['id']         = $menuId;
            $menuData['is_default'] = '10';
            $menuData['sort']       = '100';
            array_push($data, $menuData);
            # 表格组件额外新增表格规则
            if ($post['component'] === 'table/index') {
                $tableRule = [
                    'id'            => $menuId + 1,
                    'pid'           => $menuId,
                    'title'         => "{$post['title']}-表格规则",
                    'component'     => 'none/index',
                    'is_api'        => '20',
                    'path'          => "{$post['path']}GetTable",
                    'show'          => '10',
                    'method'        => ['GET'],
                    'icon'          => '',
                    'is_default'    => '10',
                    'sort'          => '100',
                    'auth_params'   => '',
                    'is_system'     => '10',
                ];
                array_push($data, $tableRule);
            }
            $data = $this->getMenusChildren($request,$data,$menuData);
            $this->saveData($data);
            return $this->success('添加成功');
        }
        $children = [
            [
                'label' => '添加',
                'value' => 'add',
            ],
            [
                'label' => '编辑',
                'value' => 'edit',
            ],
            [
                'label' => '删除',
                'value' => 'del',
            ],
        ];
        $builder = $this->formView();
        $builder->addRow('children', 'checkbox', '子级权限', [], [
            'col' => 12,
            'options' => $children
        ]);
        $view = $builder->setMethod('POST')->create();
        return $this->successRes($view);
    }
    
    /**
     * 获取子级权限
     * @param \support\Request $request
     * @param array $data
     * @param array $parent
     * @return array|object|null
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    private function getMenusChildren(Request $request,array $data,array $parent)
    {
        $children = $request->post('children',[]);
        if (empty($children)) {
            return $data;
        }
        foreach ($children as $value) {
            $parentPath = explode('/',$parent['path']);
            # 添加
            if ($value === 'add') {
                $row = end($data);
                $item   = [
                    'id'            => $row['id'] + 1,
                    'pid'           => $parent['id'],
                    'title'         => "{$parent['title']}-添加",
                    'component'     => 'form/index',
                    'is_api'        => '20',
                    'path'          => "{$parentPath[0]}/add",
                    'show'          => '10',
                    'method'        => ['GET','POST'],
                    'icon'          => '',
                    'is_default'    => '10',
                    'sort'          => '100',
                    'auth_params'   => '',
                    'is_system'     => '10',
                ];
                array_push($data, $item);
            }
            # 修改
            if ($value === 'edit') {
                $row = end($data);
                $item   = [
                    'id'            => $row['id'] + 1,
                    'pid'           => $parent['id'],
                    'title'         => "{$parent['title']}-修改",
                    'component'     => 'form/index',
                    'is_api'        => '20',
                    'path'          => "{$parentPath[0]}/edit",
                    'show'          => '10',
                    'method'        => ['GET','PUT'],
                    'icon'          => '',
                    'is_default'    => '10',
                    'sort'          => '100',
                    'auth_params'   => '',
                    'is_system'     => '10',
                ];
                array_push($data, $item);
            }
            # 删除
            if ($value === 'del') {
                $row = end($data);
                $item   = [
                    'id'            => $row['id'] + 1,
                    'pid'           => $parent['id'],
                    'title'         => "{$parent['title']}-删除",
                    'component'     => 'none/index',
                    'is_api'        => '20',
                    'path'          => "{$parentPath[0]}/del",
                    'show'          => '10',
                    'method'        => ['GET','DELETE'],
                    'icon'          => '',
                    'is_default'    => '10',
                    'sort'          => '100',
                    'auth_params'   => '',
                    'is_system'     => '10',
                ];
                array_push($data, $item);
            }
        }
        return $data;
    }

    /**
     * 修改菜单
     * @param \support\Request $request
     * @return mixed
     * @author John
     */
    public function edit(Request $request)
    {
        $id = $request->get('id', '');
        if (empty($id)) {
            return $this->fail('参数错误');
        }
        $data       = PluginMgr::getMenuList($request->plugin);
        $arrayIndex = array_search($id, array_column($data, 'id'));
        $detail     = isset($data[$arrayIndex]) ? $data[$arrayIndex] : [];
        if (empty($detail)) {
            return $this->fail('数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();
            // 数据验证
            hpValidate(SystemAuthRule::class, $post, 'edit');
            // 额外验证
            if ($post['component'] !== 'remote/index') {
                if (!isset($post['path']) || !$post['path'] && $post['is_api'] === '20') {
                    return $this->fail('请输入权限路由');
                }
                if (!isset($post['method']) || empty($post['method'])) {
                    return $this->fail('至少选择一个请求类型');
                }
            }
            # 检测菜单路由不存在斜杠
            if (strpos($post['path'], '/') === false) {
                return $this->fail('权限路由格式错误');
            }
            if (!isset($post['method'])) {
                $post['method'] = ['GET'];
            }
            $menuData = $post;
            # 处理父级菜单
            if (is_array($menuData['pid'])) {
                $menuData['pid'] = end($menuData['pid']);
            }
            # 路由地址首字母转大写
            $menuData['path']       = ucfirst($menuData['path']);
            $menuData['icon']       = isset($menuData['icon']['icon']) ? $menuData['icon']['icon'] : '';
            $menuData['id']         = $detail['id'];
            $menuData['is_default'] = $detail['is_default'];
            $menuData['sort']       = $detail['sort'];
            $data[$arrayIndex]      = $menuData;
            $this->saveData($data);
            return $this->success('修改成功');
        }
        if (is_string($detail['icon']) && !empty($detail['icon'])) {
            $detail['icon'] = [
                'icon' => $detail['icon'],
            ];
        }
        $view = $this->formView()->setMethod('PUT')->setFormData($detail)->create();
        return $this->successRes($view);
    }

    /**
     * 删除菜单
     * @param \support\Request $request
     * @return mixed
     * @author John
     */
    public function del(Request $request)
    {
        $id = $request->post('id', '');
        if (empty($id)) {
            return $this->fail('参数错误');
        }
        $data       = PluginMgr::getMenuList($request->plugin);
        $arrayIndex = array_search($id, array_column($data, 'id'));
        $detail     = isset($data[$arrayIndex]) ? $data[$arrayIndex] : [];
        if (empty($detail)) {
            return $this->fail('数据不存在');
        }
        # 删除元数据
        unset($data[$arrayIndex]);
        $this->saveData($data);
        return $this->success('删除成功');
    }

    /**
     * 保存菜单数据
     * @param array $data
     * @return mixed
     * @author John
     */
    private function saveData(array $data)
    {
        $request         = request();
        $pluginMenusPath = root_path() . "plugin/{$request->plugin}/menus.json";
        if (!file_exists($pluginMenusPath)) {
            return $this->fail('插件菜单文件不存在');
        }
        $data = Data::channelLevel($data, 0, '', 'id', 'pid');
        $data = $this->checkData($data);
        $data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($pluginMenusPath, $data);
    }

    /**
     * 递归处理数据
     * @param array $data
     * @return array
     * @author John
     */
    private function checkData(array $data)
    {
        $data = array_values($data);
        foreach ($data as $key => $value) {
            if (isset($value['_level'])) {
                unset($data[$key]['_level']);
            }
            if (isset($value['_html'])) {
                unset($data[$key]['_html']);
            }
            if (!empty($value['children'])) {
                $data[$key]['children'] = $this->checkData($value['children']);
            }
        }
        return $data;
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
            'col' => 12,
        ])
            ->addRow('pid', 'cascader', '父级菜单', [0], [
                'props' => [
                    'props' => [
                        'checkStrictly' => true,
                    ],
                ],
                'options' => self::getCascaderOptions(),
                'placeholder' => '如不选择则是顶级菜单',
                'col' => 12,
            ])
            ->addRow('component', 'select', '菜单类型', 'none/index', [
                'options' => AuthRuleRuleType::getOptions(),
                'col' => 12,
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
                'col' => 12,
            ])
            ->addRow('path', 'input', '权限路由', '', [
                'placeholder' => '示例：控制器/方法',
                'col' => 12,
            ])
            ->addRow('show', 'radio', '显示隐藏', '10', [
                'options' => ShowStatus::getOptions(),
                'col' => 12,
            ])
            ->addRow('method', 'checkbox', '请求类型', ['GET'], [
                'options' => AuthRuleMethods::getOptions(),
                'col' => 12,
            ])
            ->addComponent('icon', 'icons', '菜单图标', '', [
                'col' => 12,
            ])
            ->addRow('sort', 'input', '菜单排序', '100', [
                'col' => 12,
            ]);
        return $builder;
    }

    /**
     * 获取层级选项菜单
     * @return array
     * @author John
     */
    private static function getCascaderOptions()
    {
        $request    = request();
        $data       = PluginMgr::getMenuList($request->plugin);
        $data       = list_sort_by($data, 'sort', 'asc');
        $data       = Data::channelLevel($data, 0, '', 'id', 'pid');
        $data       = self::getChildrenOptions($data);
        $data       = array_merge([
            [
                'label' => '顶级权限菜单',
                'value' => 0
            ]
        ], $data);
        return $data;
    }

    /**
     * 递归拼接cascader组件数据
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array $data
     * @return array
     */
    public static function getChildrenOptions(array $data): array
    {
        $list = [];
        $i    = 0;
        foreach ($data as $value) {
            $componentText        = AuthRuleRuleType::getText($value['component']);
            $title                = "{$value['title']}-{$componentText}";
            $list[$i]['label']    = $title;
            $list[$i]['value']    = $value['id'];
            $list[$i]['disabled'] = false;
            if (!empty($value['disabled'])) {
                $list[$i]['disabled'] = true;
            }
            if ($value['children']) {
                $list[$i]['children'] = self::getChildrenOptions($value['children']);
            }
            $i++;
        }
        return $list;
    }
}