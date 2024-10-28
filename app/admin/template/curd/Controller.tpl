<?php

namespace plugin\{PLUGIN_NAME}\app\admin\controller;

use app\common\builder\ListBuilder;
use app\common\builder\FormBuilder;
use app\common\BaseController;
use plugin\{PLUGIN_NAME}\app\model\{CLASS_NAME};
use plugin\{PLUGIN_NAME}\app\validate\{CLASS_NAME}Validate;
use support\Request;

/**
 * {TABLE_COMMENT}
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class {CLASS_NAME}{SUFFIX} extends BaseController
{
    /**
     * 操作模型
     * @var {CLASS_NAME}
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $model;

    /**
     * 构造函数
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function __construct()
    {
        $this->model = new {CLASS_NAME};
        parent::__construct();
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
        # 实例表格
        $builder = new ListBuilder;
        # 设置分页
        $builder->pageConfig();
        {TABLE_RULE}
        # 渲染表格
        $data = $builder->create();
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
        $where = [];
        $model = $this->model;
        $data = $model
            ->order('id desc')
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
        if ($request->method() == 'POST') {
            # 获取数据
            $post = $request->post();
            # 数据验证
            hpValidate({CLASS_NAME}Validate::class, $post, 'add');
            # 保存数据
            $model = $this->model;
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        # 渲染页面
        $builder = new FormBuilder;
        $builder->setMethod('POST');
        {ADD_FORM_RULE}
        # 生成表单规则
        $data = $builder->create();
        return $this->successRes($data);
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
        $id = $request->get('id');
        $where = [
            ['id', '=', $id]
        ];
        $model = $this->model;
        $model = $model->where($where)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();

            // 数据验证
            hpValidate({CLASS_NAME}Validate::class, $post, 'edit');

            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        # 渲染页面
        $builder = new FormBuilder;
        $builder->setMethod('PUT');
        {ADD_FORM_RULE}
        # 生成表单规则
        $data = $builder->create();
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

        $where = [
            'id'        => $id,
        ];
        $model = $this->model;
        $model = $model->where($where)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if (!$model->delete()) {
            return $this->fail('删除失败');
        }
        return $this->success('删除成功');
    }
}