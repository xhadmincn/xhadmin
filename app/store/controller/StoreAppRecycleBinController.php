<?php

namespace app\store\controller;

use app\common\builder\ListBuilder;
use app\common\model\StoreApp;
use app\common\model\SystemConfig;
use app\common\model\SystemUpload;
use app\common\model\SystemUploadCate;
use app\common\BaseController;
use app\common\manager\StoreAppMgr;
use app\common\model\Store;
use app\common\model\StorePluginsExpire;
use Exception;
use support\Request;
use think\facade\Db;

/**
 * 站点管理
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-12
 */
class StoreAppRecycleBinController extends BaseController
{
    /**
     * 模型
     * @var StoreApp
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
        $this->model = new StoreApp;
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
                'api' => 'store/StoreAppRecycleBin/restore',
                'method' => 'delete',
            ], [
                'type' => 'success',
                'title' => '温馨提示',
                'content' => '是否确认恢复该项目？',
            ], [
                'type' => 'success',
                'icon' => 'RefreshRight'
            ])
            ->addRightButton('delete', '删除', [
                'type' => 'confirm',
                'api' => 'store/StoreAppRecycleBin/delete',
                'method' => 'delete',
            ], [
                'type' => 'error',
                'title' => '温馨提示',
                'content' => '是否确认删除该项目所有数据？请谨慎操作，该操作不可逆！',
            ], [
                'type' => 'danger',
                'icon' => 'RestOutlined'
            ])
            ->addScreen('title', 'input', '项目名称')
            ->addColumnEle('logo', '图标', [
                'width' => 60,
                'params' => [
                    'type' => 'image',
                ],
            ])
            ->addColumn('title', '项目名称')
            ->addColumn('auth_text', '授权', [
                'width' => 160
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
        $limit        = $request->get('limit', 12);
        $platformType = $request->get('platform', '');
        $model        = $this->model;
        $Store = Store::where(['id' => $request->user['id']])->find();
        $keyword = $request->get('keyword', '');
        $where   = [];
        if ($keyword) {
            $where[] = ['title', 'like', '%' . $keyword . '%'];
        }
        $where[] = ['store_id', '=', $Store->id];
        $data         = $model
            ->with('store')
            ->withSearch(['platform'], ['platform' => $platformType])
            ->where($where)
            ->onlyTrashed()
            ->order(['id' => 'desc'])
            ->paginate($limit)->each(function ($item) use ($Store) {
                $item->auth_text = '未授权';
                $item->auth_class = 'auth-not';
                if ($Store->plugins_name) {
                    $item->auth_text = '正常';
                    $item->auth_class = '';
                }
                $StorePluginsExpire = StorePluginsExpire::where(['id' => $item->auth_id])->find();
                if ($StorePluginsExpire) {
                    if ($StorePluginsExpire->expire_time > date('Y-m-d')) {
                        $item->auth_text = $StorePluginsExpire->expire_time . '到期';
                        $item->auth_class = '';
                    } else {
                        $item->auth_text = '已过期';
                        $item->auth_class = 'auth-expire';
                    }
                }
            });
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
        try {
            # 查询站点信息
            $where = [
                'id' => $id
            ];
            $model = $model->onlyTrashed()->where($where)->find();
            if (!$model) {
                throw new Exception('该数据不存在');
            }
            StoreAppMgr::del($model->id);
        } catch (\Throwable $e) {
            return $this->fail($e->getMessage());
        }
        return $this->success('删除成功');
    }
}
