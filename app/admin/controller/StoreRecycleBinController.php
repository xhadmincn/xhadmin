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
class StoreRecycleBinController extends BaseController
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
        $builder = new ListBuilder;
        $data    = $builder
            ->addActionOptions('操作', [
                'width' => 200
            ])
            ->pageConfig()
            ->editConfig()
            ->rowConfig([
                'height' => 70
            ])
            ->addRightButton('restore', '恢复', [
                'type' => 'confirm',
                'api' => 'admin/StoreRecycleBin/restore',
                'method' => 'delete',
            ], [
                'type' => 'success',
                'title' => '温馨提示',
                'content' => '是否确认恢复该站点？',
            ], [
                'type' => 'success',
                'icon' => 'RefreshRight'
            ])
            ->addRightButton('delete', '删除', [
                'type' => 'confirm',
                'api' => 'admin/StoreRecycleBin/delete',
                'method' => 'delete',
            ], [
                'type' => 'error',
                'title' => '温馨提示',
                'content' => '是否确认删除该站点所有数据？请谨慎操作，该操作不可逆！',
            ], [
                'type' => 'danger',
                'icon' => 'RestOutlined'
            ])
            ->addScreen('keyword', 'input', '站点名称')
            ->addColumn('title', '名称')
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
                    'type' => 'tags',
                    'options' => [
                        '10' => '冻结',
                        '20' => '正常'
                    ]
                ],
            ])
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
            ->addColumn('delete_time', '放入回收站日期', [
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
        $model = $this->model;
        $data  = $model->where($where)
            ->onlyTrashed()
            ->order(['id' => 'desc'])
            ->paginate($limit)
            ->each(function ($e) {
                return $e;
            })
            ->toArray();
        return parent::successRes($data);
    }
    public function restore(Request $request)
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
            $model = $model->onlyTrashed()->where($where)->find();
            if (!$model) {
                throw new Exception('该数据不存在');
            }
            # 恢复站点
            if (!$model->restore()) {
                throw new Exception('恢复站点失败');
            }
            Db::commit();
            return $this->success('恢复成功');
        } catch (\Throwable $e) {
            Db::rollback();
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 删除
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-30
     */
    public function delete(Request $request)
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
            $model = $model->onlyTrashed()->where($where)->find();
            if (!$model) {
                throw new Exception('该数据不存在');
            }
            # 通用查询条件
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
            SystemConfig::where($where)->delete();
            # 删除站点
            if (!$model->force()->delete()) {
                throw new Exception('删除站点失败');
            }
            Db::commit();
            return $this->success('删除成功');
        } catch (\Throwable $e) {
            Db::rollback();
            return $this->fail($e->getMessage());
        }
    }
}
