<?php

namespace app\common\trait\plugin;

use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\common\manager\PluginMgr;
use app\common\model\plugin\PluginRoles;
use app\common\utils\Json;
use Exception;
use support\Request;
use think\App;

/**
 * 角色权限
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
trait RolesTrait
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
     * @var PluginRoles
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
        $this->model = new PluginRoles;
    }

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
        $data = $builder
            ->addActionOptions('操作')
            ->pageConfig()
            ->addTopButton('add', '添加', [
                'api'           => $this->pluginPrefix . '/admin/Roles/add',
                'path'          => '/Roles/add',
            ], [], [
                'type'          => 'primary'
            ])
            ->addRightButton('auth', '授权', [
                'api'           => $this->pluginPrefix . '/admin/Roles/auth',
                'path'          => '/Roles/auth',
            ], [], [
                'type'          => 'warning',
            ])
            ->addRightButton('edit', '修改', [
                'api'           => $this->pluginPrefix . '/admin/Roles/edit',
                'path'          => '/Roles/edit',
            ], [], [
                'type'          => 'primary',
            ])
            ->addRightButton('del', '删除', [
                'type'          => 'confirm',
                'api'           => $this->pluginPrefix . '/admin/Roles/del',
                'method'        => 'delete',
            ], [
                'type'          => 'error',
                'title'         => '温馨提示',
                'content'       => '是否确认删除该数据',
            ], [
                'type'          => 'danger',
            ])
            ->addColumn('id', '序号', [
                'width'         => 90,
            ])
            ->addColumn('create_at', '创建时间')
            ->addColumn('title', '角色名称')
            ->addColumn('rule_name', '部门权限')
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
        $saas_appid = $request->saas_appid;
        $where      = [
            'saas_appid'    => $saas_appid,
            'is_system'     => '10'
        ];
        $data = $this->model->where($where)->paginate()->toArray();
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
        if ($request->method() == 'POST') {
            $post = $request->post();
            if (empty($post['title'])) {
                return $this->fail('角色名称不能为空');
            }
            $post['saas_appid'] = $this->saas_appid;
            // 默认权限
            $post['rule'] = self::getDefaultRule();
            if (!$this->model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $builder = new FormBuilder;
        $view = $builder
            ->setMethod('POST')
            ->addRow('title', 'input', '部门名称')
            ->create();
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
        $id = $request->get('id');
        $where = [
            ['id', '=', $id],
        ];
        $model = $this->model->where($where)->find();
        if (!$model) {
            return $this->fail('数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();
            if (empty($post['title'])) {
                return $this->fail('角色名称不能为空');
            }
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        if(!is_array($model->role)){
            $model->role = [];
        }
        $builder = new FormBuilder;
        $view = $builder
            ->setMethod('PUT')
            ->addRow('title', 'input', '部门名称')
            ->setData($model)
            ->create();
        return $this->successRes($view);
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
        $id = $request->post('id');
        $model = $this->model->where(['id' => $id])->find();
        if (!$model) {
            return $this->fail('数据不存在');
        }
        if ($model->is_system == '20') {
            throw new Exception('系统角色无法删除');
        }
        if ($this->model->verifyDelete($id)) {
            throw new Exception('该角色下存在管理员，无法删除');
        }
        if (!$model->delete()) {
            return $this->fail('删除失败');
        }
        return $this->success('删除成功');
    }

    /**
     * 授权
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function auth(Request $request)
    {
        $id = $request->get('id');
        $where = [
            ['id', '=', $id],
        ];
        $model = $this->model->where($where)->find();
        if (!$model) {
            return $this->fail('角色不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();
            if (empty($post['rule'])) {
                return $this->fail('规则授权错误');
            }
            $rules = $post['rule'];
            $model->rule = array_values(array_filter($rules));
            if (!$model->save()) {
                return parent::fail('保存失败');
            }
            return parent::success('保存成功');
        }
        // 获取全部权限规则
        $rules = PluginMgr::getOriginMenus($request->plugin);
        // 拼接规则权限为视图所需要的数组
        $authRule = $this->getAuthRule($rules);
        // 获取默认规则
        $defaultActive = self::getDefaultRule();
        $defaultActive = empty($defaultActive) ? [] : $defaultActive;
        // 默认选中规则 与 已授权规则合并
        if (!empty($defaultActive)) {
            $model['rule'] = array_values(array_unique(array_merge($defaultActive, $model['rule'])));
        }
        // 渲染页面
        $builder = new FormBuilder;
        $view = $builder
            ->setMethod('PUT')
            ->addRow('title', 'input', '部门名称', '', [
                'disabled'      => true
            ])
            ->addRow('rule', 'tree', '权限授权', [], [
                // 节点数据
                'data'                      => $authRule,
                // 是否默认展开所有节点
                'defaultExpandAll'          => true,
                // 	在显示复选框的情况下，是否严格的遵循父子不互相关联的做法，默认为 false
                'checkStrictly'             => true,
                // 每个树节点用来作为唯一标识的属性，整棵树应该是唯一的
                'nodeKey'                   => 'value',
            ])
            ->setData($model)
            ->create();
        return parent::successRes($view);
    }

    /**
     * 获取默认选中规则
     * @return array|string
     * @author John
     */
    private static function getDefaultRule(): array|string
    {
        $request = request();
        $data = PluginMgr::getOriginMenus($request->plugin);
        $systemMenus   = [];
        foreach ($data as $value) {
            if ($value['is_system'] == '20' && $value['is_default'] == '20') {
                $systemMenus[] = $value['path'];
            }
        }
        return empty($systemMenus) ? '' : $systemMenus;
    }

    /**
     * 获取权限列表
     * @param array $rule
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    private function getAuthRule(array $rule): array
    {
        $data = [];
        $i = 0;
        foreach ($rule as $value) {
            // 默认选选中
            $disabled = $value['is_default'] === '20' ? true : false;
            // 组装树状格式数据
            $label                = $value['title'];
            if ($value['path']) {
                $label .= "（{$value['path']}）";
            }
            $data[$i]['title']          = $label;
            $data[$i]['value']          = $value['path'];
            $data[$i]['disabled']       = $disabled;
            if (isset($value['children']) && !empty($value['children'])) {
                $data[$i]['children']   = $this->getAuthRule($value['children']);
            }
            $i++;
        }
        return $data;
    }
}
