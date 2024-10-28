<?php
namespace app\common\trait\plugin;

use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\common\model\plugin\PluginTags;
use app\common\utils\Data;
use app\common\utils\Json;
use Exception;
use support\Request;
use think\App;
use app\common\manager\PluginMgr;

/**
 * 单页系统
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
trait TagsTrait
{
    # 使用JSON工具类
    use Json;

    /**
     * 应用ID（null则获取系统配置）
     * @var int|null
     */
    protected $saas_appid = null;

    /**
     * 模型
     * @var PluginTags
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
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
        $this->model = new PluginTags;
    }

    /**
     * 模型
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function indexGetTable(Request $request)
    {
        $builder = new ListBuilder;
        $data    = $builder
            ->addActionOptions('操作', [
                'width' => 180
            ])
            ->pageConfig()
            ->addTopButton('add', '添加', [
                'api' => $this->pluginPrefix . '/admin/Tags/add',
                'path' => '/Tags/add',
            ], [], [
                'type' => 'primary'
            ])
            ->addRightButton('edit', '修改', [
                'api' => $this->pluginPrefix . '/admin/Tags/edit',
                'path' => '/Tags/edit',
            ], [], [
                'type' => 'primary',
            ])
            ->addRightButton('del', '删除', [
                'type' => 'confirm',
                'api' => $this->pluginPrefix . '/admin/Tags/del',
                'method' => 'delete',
            ], [
                'type' => 'error',
                'title' => '温馨提示',
                'content' => '是否确认删除该数据',
            ], [
                'type' => 'danger',
            ])
            ->addColumn('create_at', '创建时间', [
                'width' => 150
            ])
            ->addColumn('title', '标题名称')
            ->addColumn('name', '标签名称', [
                'width' => 280
            ])
            ->addColumnEle('link', 'H5链接', [
                'params' => [
                    'type' => 'link',
                    'props' => [
                        'copy' => true,
                        'text' => '点击打开'
                    ]
                ]
            ])
            ->addColumnEle('status', '状态', [
                'width' => 100,
                'params' => [
                    'type' => 'switch',
                    'api' => $this->pluginPrefix . '/admin/Tags/rowEdit',
                    'unchecked' => [
                        'text' => '禁用',
                        'value' => '10'
                    ],
                    'checked' => [
                        'text' => '正常',
                        'value' => '20'
                    ],
                ],
            ])
            ->addColumnEle('is_menu', '是否加入菜单', [
                'width' => 150,
                'params' => [
                    'type' => 'switch',
                    'api' => $this->pluginPrefix . '/admin/Tags/menuEdit',
                    'unchecked' => [
                        'text' => '未加入菜单',
                        'value' => '10'
                    ],
                    'checked' => [
                        'text' => '已加入菜单',
                        'value' => '20'
                    ],
                ],
            ])
            ->create();
        return $this->successRes($data);
    }

    /**
     * 列表
     * @param \support\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function index(Request $request)
    {
        $order = 'sort asc,id desc';
        $menus = PluginMgr::getMenuList($request->plugin);
        $data  = $this->model->order($order)->paginate()->each(function ($item) use ($menus) {
            $auth_params = "tag_name={$item['name']}";
            $arrayIndex = array_search($auth_params, array_column($menus, 'auth_params'));
            $isMenu = '20';
            if ($arrayIndex === false) {
                $isMenu = '10';
            }
            $item->is_menu = $isMenu;
            return $item;
        });
        return $this->successRes($data);
    }

    /**
     * 单页菜单编辑
     * @param \support\Request $request
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function menuEdit(Request $request)
    {
        $keyField = $request->post('keyField');
        $id       = $request->post('id');
        $value    = $request->post('value');
        $where    = [
            $keyField => $id
        ];
        $model    = $this->model;
        $model    = $model->where($where)->find();
        if (!$model) {
            return $this->fail('数据不存在');
        }
        $menus = PluginMgr::getMenuList($request->plugin);
        $menus = list_sort_by($menus, 'id', 'asc');
        # 检测是否已存在菜单
        $column      = array_column($menus, 'auth_params');
        $column      = array_filter($column);
        $auth_params = "tag_name={$model['name']}";
        if ($value === '20' && !in_array($auth_params, $column)) {
            $menuData   = end($menus);
            $arrayIndex = array_search('Content/group', array_column($menus, 'path'));
            $parent     = $menus[$arrayIndex];
            # 增加菜单
            $menuData = [
                'id' => $menuData['id'] + 1,
                'pid' => $parent['id'],
                'title' => $model->menu_title,
                'component' => "form/index",
                'is_api' => '20',
                'method' => ['GET', 'POST', 'PUT'],
                'path' => "Tags/edit",
                'show' => '20',
                'is_default' => '10',
                'is_system' => '20',
                'sort' => '100',
                'auth_params' => $auth_params,
                'icon' => '',
                'children' => []
            ];
            array_push($menus, $menuData);
        } else {
            # 删除菜单
            $arrayIndex = array_search($auth_params, array_column($menus, 'auth_params'));
            if (isset($menus[$arrayIndex])) {
                unset($menus[$arrayIndex]);
            }
        }
        # 保存菜单数据
        $this->saveData($menus);
        # 返回结果
        return $this->success('操作成功');
    }

    /**
     * 添加
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function add(Request $request)
    {
        if ($request->method() == 'POST') {
            $post = $request->post();

            # 数据验证
            if (empty($post['title'])) {
                return $this->fail('请输入标题名称');
            }
            if (empty($post['name'])) {
                return $this->fail('请输入标签名称');
            }

            # 验证是否已存在
            $where = [
                'name' => $post['name']
            ];
            if ($this->model->where($where)->count()) {
                return $this->fail('该单页已存在');
            }

            $model = $this->model;
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $data = $this->getFormView()
            ->setMethod('POST')
            ->create();
        return $this->successRes($data);
    }

    /**
     * 修改
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function edit(Request $request)
    {
        $id       = $request->get('id', '');
        $tag_name = $request->get('tag_name', '');
        $where    = [
            'id' => $id
        ];
        if ($tag_name) {
            $where = [
                'name' => $tag_name
            ];
        }
        $model = $this->model->where($where)->find();
        if (!$model) {
            throw new Exception('数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();

            # 数据验证
            if (empty($post['title'])) {
                return $this->fail('请输入标题名称');
            }
            if (empty($post['name'])) {
                return $this->fail('请输入标签名称');
            }

            # 验证是否已存在
            $where = [
                ['id', '<>', $model['id']],
                ['name', '=', $post['name']]
            ];
            if ($this->model->where($where)->count()) {
                return $this->fail('该单页已存在');
            }

            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $data = $this->getFormView()
            ->setMethod('PUT')
            ->setData($model)
            ->create();
        return $this->successRes($data);
    }

    /**
     * 删除
     * @param \support\Request $request
     * @throws \Exception
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function del(Request $request)
    {
        $id    = $request->post('id', '');
        $where = [
            'id' => $id
        ];
        $model = $this->model->where($where)->find();
        if (!$model) {
            throw new Exception('数据不存在');
        }
        if (!$model->delete()) {
            return $this->fail('删除失败');
        }
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
     * 获取表单视图
     * @return FormBuilder
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private function getFormView()
    {
        $id = request()->get('id', '');
        $tag_name = request()->get('tag_name', '');
        $disabled = false;
        if ($id) {
            $disabled = true;
        }
        if ($tag_name && !$disabled) {
            $disabled = true;
        }
        $builder = new FormBuilder;
        $builder->addRow('title', 'input', '标题名称', '', [
            'col'           => 8,
        ]);
        $builder->addRow('menu_title', 'input', '菜单标题', '', [
            'col'           => 8,
        ]);
        $builder->addRow('name', 'input', '标签名称（创建后不可修改）', '', [
            'col'           => 8,
            'disabled'      => $disabled,
        ]);
        $builder->addComponent('content', 'wangEditor', '文章内容');
        return $builder;
    }
}