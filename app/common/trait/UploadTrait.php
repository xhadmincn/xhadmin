<?php

namespace app\common\trait;

use app\common\builder\FormBuilder;
use app\common\model\SystemUpload;
use app\common\service\UploadService;
use app\common\utils\Json;
use support\Request;

/**
 * 系统设置管理
 * @author 贵州猿创科技有限公司
 * @copyright (c) 2023
 */
trait UploadTrait
{
    // 使用JSON工具类
    use Json;

    /**
     * 请求对象
     * @var Request
     */
    protected $request = null;

    /**
     * 应用ID（null则获取系统配置）
     * @var int|null
     */
    protected $saas_appid = null;

    /**
     * 站点ID（null则获取系统配置）
     * @var int|null
     */
    protected $store_id = null;

    /**
     * 用户ID（null则获取系统配置）
     * @var int|null
     */
    protected $uid = null;
    /**
     * 允许上传的文件格式
     * 图片：jpg,jpeg,png,gif,bmp,webp,svg
     * 音频：mp3
     * 视频：mp4
     * 文档：doc,docx,xls,xlsx,ppt,pptx,pdf
     * 证书：pem,crt,key
     * 压缩：zip,rar,7z
     * 字体：ttf,otf,woff,woff2,eot
     * 
     * @var array
     */
    protected $acceptExt = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'mp3', 'mp4', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'pem', 'crt', 'key', 'zip', 'rar', '7z', 'ttf', 'otf', 'woff', 'woff2', 'eot'];

    /**
     * 获取附件列表
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function index(Request $request)
    {
        $cid     = $request->get('cid', '');
        $suffix  = $request->get('suffix', '*');
        $limit = $request->get('limit', 10);
        $order = $request->get('order', 'desc');
        # 查询条件组装
        $where[] = ['saas_appid', '=', $this->saas_appid];
        $where[] = ['store_id', '=', $this->store_id];
        $where[] = ['hide', '=', 0];
        if ($this->uid) {
            $where[] = ['uid', '=', $this->uid];
        }
        # 取出对后缀格式
        if ($suffix !== '*' && !empty($suffix)) {
            $where[] = ['format', 'in', $suffix];
        }
        if ($cid) {
            $where[] = ['cid', '=', $cid];
        }
        $data  = SystemUpload::with(['category'])
            ->where($where)
            ->order("update_at {$order},id asc")
            ->paginate($limit)
            ->toArray();
        return parent::successRes($data);
    }

    /**
     * 修改附件
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function edit(Request $request)
    {
        $id    = $request->get('id', '');
        $where = [
            ['id', '=', $id]
        ];
        $model = SystemUpload::where($where)->find();
        if (!$model) {
            return $this->fail('该附件不存在');
        }
        if ($request->method() === 'PUT') {
            $post = $request->post();
            if (!$model->save($post)) {
                return $this->fail('修改失败');
            }
            return $this->success('修改成功');
        } else {
            $builder = new FormBuilder;
            $data    = $builder
                ->setMethod('PUT')
                ->addRow('title', 'input', '附件名称')
                ->addRow('path', 'input', '文件地址', '', [
                    'disabled' => true,
                ])
                ->addRow('filename', 'input', '文件名称', '', [
                    'disabled' => true,
                    'col' => [
                        'span' => 12
                    ]
                ])
                ->addRow('format', 'input', '文件格式', '', [
                    'disabled' => true,
                    'col' => [
                        'span' => 12
                    ]
                ])
                ->addRow('size_format', 'input', '文件大小', '', [
                    'disabled' => true,
                    'col' => [
                        'span' => 12
                    ]
                ])
                ->addRow('adapter', 'input', '选定器', '', [
                    'disabled' => true,
                    'col' => [
                        'span' => 12
                    ]
                ])
                ->setData($model)
                ->create();
            return $this->successRes($data);
        }
    }

    /**
     * 删除选中附件
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function del(Request $request)
    {
        $id = (int) $request->post('id', 0);
        $ids = $request->post('ids', []);
        if (!$id && empty($ids)) {
            return $this->fail('请选择需要删除的附件');
        }
        if (empty($ids)) {
            if (!UploadService::delete($id)) {
                return $this->fail('删除失败');
            }
        }
        # 批量删除
        foreach ($ids as $id) {
            UploadService::delete($id);
        }
        return $this->success('删除完成');
    }


    /**
     * 上传附件
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function upload(Request $request)
    {
        # 获取上传文件
        $file = $request->file('file');
        # 获取上传目录
        $dirName  = $request->post('dir_name', '');
        if (isset($this->request->uid)) {
            $this->uid = $this->request->uid;
        }
        $is_hide = $request->post('is_hide', 0);
        # 上传附件
        try {
            $data    = UploadService::upload($file, $dirName, $this->saas_appid, $this->uid, $this->store_id, $is_hide,$this->acceptExt);
            if (!$data) {
                return $this->fail('上传失败');
            }
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage());
        }
        return $this->successFul('上传成功', $data);
    }

    /**
     * 移动选中附件
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function move(Request $request)
    {
        $cid = $request->post('cid');
        $ads   = $request->post('ids');
        if (!$cid) {
            return $this->fail('请选择移动的分类');
        }
        if (!$ads) {
            return $this->fail('附件选择错误');
        }
        if (!is_array($ads)) {
            return $this->fail('请选择移动的附件');
        }
        $where[] = ['id', 'in', $ads];
        SystemUpload::where($where)->save(['cid' => $cid]);
        return $this->success('附件移动完成');
    }
}
