<?php

namespace app\admin\controller;

use app\common\BaseController;
use app\common\builder\ListBuilder;
use app\common\manager\CurdMgr;
use app\common\manager\ModulesMgr;
use app\common\model\SystemCurd;
use app\common\exception\RedirectException;
use app\common\manager\DbMgr;
use app\common\service\FrameworkService;
use Exception;
use support\Request;
use think\helper\Str;

/**
 * CRUD一键生成
 * @author 贵州猿创科技有限公司
 * @copyright (c) 贵州猿创科技有限公司
 */
class CurdController extends BaseController
{
    # 不带前缀表名
    private $tableName;
    # 带前缀表名
    private $prefixTableName;
    # 数据库名称
    private $database;

    /**
     * 构造函数
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function __construct()
    {
        $request               = request();
        $this->database        = config('database.connections.mysql.database');
        $prefix                = config('database.connections.mysql.prefix');
        $this->prefixTableName = $request->input('TABLE_NAME', '');
        if (strpos($this->prefixTableName, $prefix) === false) {
            $this->tableName       = $this->prefixTableName;
            $this->prefixTableName = "{$prefix}{$this->prefixTableName}";
        } else {
            $this->tableName = str_replace($prefix, '', $this->prefixTableName);
        }
        if (!DbMgr::hasTable($this->tableName)) {
            throw new Exception('该数据表不存在');
        }
        if (in_array($this->tableName, ModulesMgr::dropTables())) {
            throw new RedirectException('系统表，禁止操作字段', '/Modules/index');
        }
    }

    /**
     * CURD列表
     * @param \support\Request $request
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function indexGetTable(Request $request)
    {
        $builder = new ListBuilder;
        $data    = $builder
            ->pageConfig()
            ->rowConfig([
                'keyField' => 'field_name'
            ])
            ->addTopButton('add', '构建CURD', [
                'type' => 'remote',
                'modal' => true,
                'api' => 'admin/SystemCurd/add',
                'path' => 'remote/curd',
                'queryParams' => [
                    'TABLE_NAME' => $this->tableName,
                ],
            ], [
                'title' => '构建CURD',
                'style' => [
                    'width' => '60%',
                    'height' => '650px',
                    'showIcon' => false,
                    'maskClosable' => false,
                ],
            ], [
                'type' => 'primary',
            ])
            ->addColumn('field_name', '字段名称')
            ->addColumn('field_comment', '字段备注')
            ->addColumnEle('list_title', '列表名称', [
                'params' => [
                    'type' => 'input',
                    'api' => "/admin/SystemCurd/edit?TABLE_NAME={$this->tableName}",
                    'props' => [],
                ],
            ])
            ->addColumnEle('list_type', '表格数据', [
                'width' => 100,
                'params' => [
                    'type' => 'switch',
                    'api' => "/admin/SystemCurd/edit?TABLE_NAME={$this->tableName}",
                    'checked' => [
                        'text' => '显示',
                        'value' => '20'
                    ],
                    'unchecked' => [
                        'text' => '不显示',
                        'value' => '10'
                    ]
                ],
            ])
            ->addColumnEle('form_add', '增加表单', [
                'width' => 100,
                'params' => [
                    'type' => 'switch',
                    'api' => "/admin/SystemCurd/edit?TABLE_NAME={$this->tableName}",
                    'checked' => [
                        'text' => '显示',
                        'value' => '20'
                    ],
                    'unchecked' => [
                        'text' => '不显示',
                        'value' => '10'
                    ]
                ],
            ])
            ->addColumnEle('form_edit', '修改表单', [
                'width' => 100,
                'params' => [
                    'type' => 'switch',
                    'api' => "/admin/SystemCurd/edit?TABLE_NAME={$this->tableName}",
                    'checked' => [
                        'text' => '显示',
                        'value' => '20'
                    ],
                    'unchecked' => [
                        'text' => '不显示',
                        'value' => '10'
                    ]
                ],
            ])
            ->addColumnEle('is_del', '删除数据', [
                'width' => 100,
                'params' => [
                    'type' => 'switch',
                    'api' => "/admin/SystemCurd/edit?TABLE_NAME={$this->tableName}",
                    'checked' => [
                        'text' => '支持',
                        'value' => '20'
                    ],
                    'unchecked' => [
                        'text' => '不支持',
                        'value' => '10'
                    ]
                ],
            ])
            ->create();
        return $this->successRes($data);
    }

    /**
     * 获取CRUD列表
     * @param \support\Request $request
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function index(Request $request)
    {
        # 分页参数
        $page   = (int) $request->get('page', 1);
        $limit  = (int) $request->get('limit', 20);
        $offset = ($page - 1) * $limit;
        # 数据参数
        $database = config('database.connections.mysql.database');
        $table    = $this->prefixTableName;
        # 数据表字段
        $list = DbMgr::instance()->select("SELECT TABLE_NAME as table_name,COLUMN_NAME as field_name,COLUMN_COMMENT as field_comment FROM information_schema.COLUMNS where TABLE_SCHEMA = '{$database}' and table_name = '{$table}' order by ORDINAL_POSITION limit {$offset},{$limit}");
        # 处理字段参数
        $i = 0;
        foreach ($list as $object) {
            if (is_object($object)) {
                $list[$i] = (array) $object;
            }
            $where      = [
                'table_name' => $this->tableName,
                'field_name' => $list[$i]['field_name'],
            ];
            $field      = [
                'table_name',
                'field_name',
                'list_title',
                'list_type',
                'form_add',
                'form_edit',
                'is_del',
            ];
            $fieldModel = SystemCurd::where($where)
                ->field($field)
                ->find();
            $list[$i]   = array_merge($list[$i], [
                'list_type' => '',
                'list_extra' => '',
                'form_type' => 'input',
                'form_add' => '',
                'form_edit' => '',
                'form_extra_add' => '',
                'form_extra_edit' => '',
            ]);
            if ($fieldModel) {
                $list[$i] = array_merge($list[$i], $fieldModel->toArray());
            }
            $i++;
        }
        # 获取总数
        $total = DbMgr::instance()->select("SELECT count(*) as total from information_schema.COLUMNS WHERE table_schema='{$database}' and table_name='{$table}';")[0]->total ?? 0;
        # 返回数据
        $data = [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => $total,
            'data' => $list
        ];
        return $this->successRes($data);
    }

    /**
     * 生成一键CURD
     * @param \support\Request $request
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function add(Request $request)
    {
        try {
            # 获取参数
            $tableName = ucfirst($this->tableName);
            $app_name  = $request->post('app_name', '');
            $is_cover  = (int) $request->post('is_cover', '0');
            $menu_id   = $request->post('menu_id', '');
            $menu_name = $request->post('menu_name', '');
            if ($app_name === '') {
                return $this->fail('请选择构建至某个应用');
            }
            if ($menu_id === '') {
                return $this->fail('请选择构建至某个菜单');
            }
            # 获取CURD代码及目标路径
            $curdData = CurdMgr::getCurdCode($app_name, $tableName);
            if (file_exists(base_path($curdData['controllerPath'])) && $is_cover === 0) {
                throw new Exception('该控制器已生成');
            }
            # 构建控制器
            if (!file_put_contents(base_path($curdData['controllerPath']), $curdData['controller'])) {
                throw new Exception('生成控制器失败');
            }
            # 构建模型
            if (file_exists(base_path($curdData['modelPath'])) && $is_cover === 0) {
                throw new Exception('该模型已生成');
            }
            if (!file_put_contents(base_path($curdData['modelPath']), $curdData['model'])) {
                throw new Exception('生成模型失败');
            }
            # 构建验证器
            if (file_exists(base_path($curdData['validatePath'])) && $is_cover === 0) {
                throw new Exception('该验证器已生成');
            }
            if (!file_put_contents(base_path($curdData['validatePath']), $curdData['validate'])) {
                throw new Exception('生成验证器失败');
            }
            # 是否生成菜单
            if ($menu_id !== 'cancel') {
                if (!$menu_name) {
                    throw new Exception('请填写菜单名称');
                }
                $this->createdMenu($app_name, $tableName, (int) $menu_id, $menu_name);
            }
            # 平滑重启服务
            FrameworkService::reloadSubprocess();
            # 返回成功
            return $this->success('CURD操作完成');
        } catch (\Throwable $e) {
            # 回滚文件
            # 返回错误
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 生成菜单
     * @param string $app_name
     * @param string $tableName
     * @param int $pid
     * @param string $menu_name
     * @throws \Exception
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private function createdMenu(string $app_name, string $tableName, int $pid, string $menu_name)
    {
        $menuPath = base_path("/plugin/{$app_name}/menu.json");
        $menus    = CurdMgr::getMenusPreView(
            $menuPath,
            $this->database,
            $this->prefixTableName,
            $app_name,
            $tableName,
            $pid,
            $menu_name
        );
        $content  = json_encode($menus, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if (!file_put_contents($menuPath, $content)) {
            throw new Exception('生成菜单失败');
        }
    }


    /**
     * 编辑CRUD列表
     * @param \support\Request $request
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function edit(Request $request)
    {
        if ($request->method() !== 'PUT') {
            return $this->fail('请求方式错误');
        }
        # 获取参数
        $table_name = $this->tableName;
        $field_name = $request->post('field_name', '');
        $field      = $request->post('field', '');
        $value      = $request->post('value', '');
        # 查询参数
        $model = SystemCurd::where([
            'table_name' => $table_name,
            'field_name' => $field_name,
        ])->find();
        if ($model) {
            $model->$field = $value;
        } else {
            $sql       = "SELECT * FROM information_schema.COLUMNS WHERE table_name = ('{$this->prefixTableName}') and COLUMN_NAME = '{$field_name}' ORDER BY ordinal_position";
            $columnObj = DbMgr::instance()->select($sql);
            if (empty($columnObj)) {
                throw new RedirectException('获取字段数据失败', "/Fields/index?TABLE_NAME={$this->prefixTableName}");
            }
            isset($columnObj[0]) && $columnData = (array) $columnObj[0];
            if (empty($columnData)) {
                throw new RedirectException('字段数据出错', "/Fields/index?TABLE_NAME={$this->prefixTableName}");
            }
            $model                = new SystemCurd;
            $model->table_name    = $table_name;
            $model->field_name    = $field_name;
            $model->list_sort     = $columnData['ORDINAL_POSITION'];
            $model->field_comment = $columnData['COLUMN_COMMENT'];
            $model->$field        = $value;
        }
        if (!$model->save()) {
            return $this->fail('修改失败');
        }
        return $this->success('修改完成');
    }

    /**
     * 获取CURD详情
     * @param \support\Request $request
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function detail(Request $request)
    {
        $data = CurdMgr::getPlugins();
        return $this->successRes($data);
    }

    /**
     * 获取应用菜单列表
     * @param \support\Request $request
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function getPluginMenus(Request $request)
    {
        $app_name  = $request->get('app_name', '');
        $className = Str::studly($this->tableName);
        if (empty($app_name)) {
            return $this->fail('请选择应用');
        }
        $menuPath = base_path("/plugin/{$app_name}/menu.json");
        if (!file_exists($menuPath)) {
            return $this->fail('应用菜单文件不存在');
        }
        $menuData = json_decode(file_get_contents($menuPath), true);
        $data = CurdMgr::parentLevelList($menuData, $className);
        $data = array_merge([
            [
                'label' => '不构建菜单',
                'value' => 'cancel'
            ],
            [
                'label' => '构建顶级菜单',
                'value' => 'top'
            ],
        ], $data);
        return $this->successRes($data);
    }

    /**
     * 获取CURD代码预览
     * @param \support\Request $request
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function getPreview(Request $request)
    {
        # 获取参数
        $app_name  = $request->get('app_name', '');
        $pidIndex    = $request->get('menu_id', '');
        $menu_name = $request->get('menu_name', '');
        # 数据验证
        if (empty($app_name)) {
            return $this->fail('请选择应用');
        }
        if ($pidIndex === '') {
            return $this->fail('请选择构建至某个菜单');
        }
        # 组装参数
        $tableName     = ucfirst($this->tableName);
        $menuPath      = "/plugin/{$app_name}/menu.json";
        $menuPathRoot  = base_path($menuPath);
        $data['menus'] = [
            'path' => '',
            'menus' => ''
        ];
        $data['code']  = CurdMgr::getCurdCode($app_name, $tableName);
        # 是否生成菜单
        if ($pidIndex !== 'cancel') {
            if (!$menu_name) {
                return $this->fail('请输入菜单名称');
            }
            $indexPath = [];
            if ($pidIndex !== 'top') {
                $indexPath= explode('-', $pidIndex);
            }
            $menuView               = CurdMgr::getMenusPreView(
                $menuPathRoot,
                $this->database,
                $this->prefixTableName,
                $app_name,
                $tableName,
                $indexPath,
                (string) $menu_name
            );
            $content                = json_encode($menuView, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $data['menus']['path']  = $menuPath;
            $data['menus']['menus'] = $content;
        }
        return $this->successRes($data);
    }
}