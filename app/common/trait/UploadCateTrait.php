<?php

namespace app\common\trait;

use app\common\builder\FormBuilder;
use app\common\model\SystemUpload;
use app\common\model\SystemUploadCate;
use app\common\utils\Json;
use support\Request;

/**
 * 附件分类管理
 * @author 贵州猿创科技有限公司
 * @copyright (c) 2023
 */
trait UploadCateTrait
{
    // 使用JSON工具类
    use Json;

    /**
     * 请求对象
     * @var Request
     */
    protected $request = null;

    /**
     * 站点ID（null则获取系统配置）
     * @var int|null
     */
    protected $store_id = null;

    /**
     * 应用ID（null则获取系统配置）
     * @var int|null
     */
    protected $saas_appid = null;

    /**
     * 用户ID
     * @var int|null
     */
    protected $uid = null;

    /**
     * 上传分类列表
     * @param \support\Request $request
     * @return mixed
     * @author John
     */
    public function index(Request $request)
    {
        $order = $request->get('order', 'asc');
        $where[] = ['saas_appid', '=', $this->saas_appid];
        $where[] = ['store_id', '=', $this->store_id];
        $where[] = ['uid', '=', $this->uid];
        $data = SystemUploadCate::where($where)
            ->order("sort {$order},id asc")
            ->select()
            ->toArray();
        return $this->successRes($data);
    }

    /**
     * 添加
     * @param \support\Request $request
     * @return mixed
     * @author John
     */
    public function add(Request $request)
    {
        if ($request->method() === 'POST') {
            $post = $request->post();
            $post['store_id']       = $this->store_id;
            $post['saas_appid']     = $this->saas_appid;
            $post['uid']            = $this->uid;
            $post['is_system']      = '10';
            // 数据验证
            hpValidate(\app\admin\validate\SystemUploadCate::class, $post, 'add');

            $model = new SystemUploadCate;
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $builder = new FormBuilder;
        $data    = $builder
            ->setMethod('POST')
            ->addRow('title', 'input', '分类名称')
            ->addRow('dir_name', 'input', '分类标识')
            ->addRow('sort', 'input', '分类排序', '0')
            ->create();
        return $this->successRes($data);
    }

    /**
     * 编辑
     * @param \support\Request $request
     * @return mixed
     * @author John
     */
    public function edit(Request $request)
    {
        $id    = $request->get('id', '');
        $model = SystemUploadCate::where(['id' => $id])->find();
        if (!$model) {
            return $this->fail('该分类不存在');
        }
        if ($request->method() === 'PUT') {
            $post = $request->post();
            // 数据验证
            hpValidate(\app\admin\validate\SystemUploadCate::class, $post, 'edit');
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $builder = new FormBuilder;
        $data    = $builder
            ->setMethod('PUT')
            ->addRow('title', 'input', '分类名称')
            ->addRow('dir_name', 'input', '分类标识', '', [
                'disabled' => true,
            ])
            ->addRow('sort', 'input', '分类排序')
            ->setData($model)
            ->create();
        return $this->successRes($data);
    }

    /**
     * 删除
     * @param \support\Request $request
     * @return mixed
     * @author John
     */
    public function del(Request $request)
    {
        $id    = $request->post('id', '');
        if (!$id) {
            return $this->fail('缺少参数');
        }
        $model = new SystemUploadCate;
        $where = [
            ['id', '=', $id],
        ];
        if ($this->saas_appid) {
            $where[] = ['saas_appid', '=', $this->saas_appid];
        }
        if ($this->store_id) {
            $where[] = ['store_id', '=', $this->store_id];
        }
        if ($this->uid) {
            $where[] = ['uid', '=', $this->uid];
        }
        $model = $model->where($where)->find();
        if (!$model) {
            return $this->fail('该附件分类不存在');
        }
        if ($model->is_system === '20') {
            return $this->fail('系统分类，禁止删除');
        }
        if (SystemUpload::where('cid', $id)->count()) {
            return $this->fail('该分类下已有附件，禁止删除');
        }
        if (!$model->delete()) {
            return $this->fail('删除失败');
        }
        return $this->success('删除成功');
    }
}
