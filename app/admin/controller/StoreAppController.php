<?php

namespace app\admin\controller;

use app\common\builder\FormBuilder;
use app\admin\model\Store;
use app\common\model\StorePlugins;
use app\common\model\StorePluginsExpire;
use app\common\manager\StoreAppMgr;
use app\common\service\SystemInfoService;
use app\common\BaseController;
use support\Request;
use think\facade\Db;

/**
 * 租户授权应用
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-05-09
 */
class StoreAppController extends BaseController
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
    public function __construct()
    {
        $this->model = new Store;
    }

    /**
     * 租户应用授权
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-09
     */
    public function index(Request $request)
    {
        $store_id = (int)$request->get('store_id', 0);
        $model = $this->model;
        $where = [
            'id' => $store_id
        ];
        $model = $model->where($where)->find();
        if (!$model) {
            return $this->fail('找不到该租户数据');
        }
        if ($request->method() == 'PUT') {
            /* 
            * 请勿修改此处代码，包括引用的类库
            * 非官方授权改动，破解等行为，官方不提供任何技术支持并且保留追究责任的权利
            * 包括但不限于追究法律责任，取消授权，停止服务等
            */
            try {
                $plugins = StoreAppMgr::getBuyInstallApp();
                $authNum = [];
                foreach ($plugins as $key => $value) {
                    $authNum[$value['name']] = $value['auth_num'];
                }
            } catch (\Throwable $e) {
                return $this->fail($e->getMessage());
            }
            Db::startTrans();
            try {
                $model->plugins_name = '';
                $model->save();
                $plugins = $request->post('plugins');
                foreach ($plugins as $key => $value) {
                    $storePlugins = StorePlugins::where(['store_id' => $model->id, 'plugin_name' => $value['name']])->find();
                    if (!$storePlugins) {
                        $storePlugins = new StorePlugins;
                        $storePlugins->store_id = $model->id;
                    }
                    $storePlugins->plugin_name = $value['name'];
                    $storePlugins->plugin_title = $value['title'];
                    $storePlugins->save();
                    $use_auth_num = 0;
                    foreach ($value['form'] as $item) {
                        if (empty($item['auth_num']) || empty($item['expire_time'])) {
                            throw new \Exception($value['title'] . '：授权数量和授权时间不能为空');
                        }
                        $auth_num = abs((int)$item['auth_num']);
                        if ($auth_num < 1) {
                            continue;
                        }
                        $expire_time = $item['expire_time'];
                        if (!strtotime($expire_time)) {
                            continue;
                        }
                        if ($expire_time < date('Y-m-d')) {
                            continue;
                        }
                        $id = null;
                        if (!empty($item['id'])) {
                            $id = (int)$item['id'];
                        }
                        if ($id) {
                            $storePluginsExpire = StorePluginsExpire::where(['id' => $id])->find();
                        } else {
                            $storePluginsExpire = new StorePluginsExpire;
                            $storePluginsExpire->store_id = $model->id;
                            $storePluginsExpire->store_plugins_id = $storePlugins->id;
                        }
                        $storePluginsExpire->auth_num = $auth_num;
                        $storePluginsExpire->expire_time = $item['expire_time'];
                        $storePluginsExpire->save();
                        $use_auth_num += $auth_num;
                    }
                    $storePlugins = StorePlugins::where(['id' => $storePlugins->id])->find();
                    $storePlugins->auth_num = $use_auth_num;
                    $storePlugins->save();
                    $useAuthNum = StorePlugins::where(['plugin_name' => $value['name']])->sum('auth_num');
                    if ($useAuthNum > $authNum[$value['name']]) {
                        throw new \Exception($value['title'] . '：授权数量超出购买数量');
                    }
                }
                Db::commit();
            } catch (\Throwable $th) {
                Db::rollback();
                return $this->fail($th->getMessage());
            }
            return $this->success('授权成功');
            /* 
            * 请勿修改此处代码，包括引用的类库
            * 非官方授权改动，破解等行为，官方不提供任何技术支持并且保留追究责任的权利
            * 包括但不限于追究法律责任，取消授权，停止服务等
            */
        }
        $plugins = [];
        $pluginsData = [];
        $StorePlugins = StorePlugins::where(['store_id' => $model->id])->select();
        foreach ($StorePlugins as $item) {
            $form = [];
            $StorePluginsExpire = StorePluginsExpire::where(['store_id' => $model->id, 'store_plugins_id' => $item->id])->select();
            foreach ($StorePluginsExpire as $value) {
                $form[] = [
                    'id' => $value->id,
                    'auth_num' => $value->auth_num,
                    'expire_time' => $value->expire_time
                ];
            }
            if (empty($form)) {
                $form[] = [
                    'auth_num' => 0,
                    'expire_time' => ''
                ];
            }
            $pluginsData[] = [
                'id' => $item->id,
                'name' => $item->plugin_name,
                'title' => $item->plugin_title,
                'logo' => $item->plugin_logo,
                'auth_num' => 0,
                'use_auth_num' => 0,
                'stock_auth_num' => 0,
                'form' => $form
            ];
        }
        $defaultData = [];
        try {
            $plugins = StoreAppMgr::getBuyInstallApp();
            foreach ($plugins as $key => $value) {
                $use_auth_num = StorePlugins::where(['plugin_name' => $value['name']])->sum('auth_num');
                $plugins[$key]['use_auth_num'] = $use_auth_num;
                if (empty($pluginsData)) {
                    if ($model->plugins_name && in_array($value['name'], $model->plugins_name)) {
                        $defaultData[] = [
                            'name' => $value['name'],
                            'title' => $value['title'],
                            'logo' => $value['logo'],
                            'auth_num' => $value['auth_num'],
                            'use_auth_num' => $use_auth_num,
                            'stock_auth_num' => $value['auth_num'] - $use_auth_num,
                            'form' => [
                                [
                                    'auth_num' => 0,
                                    'expire_time' => ''
                                ]
                            ]
                        ];
                    }
                } else {
                    foreach ($pluginsData as $key => $item) {
                        if ($item['name'] == $value['name']) {
                            $pluginsData[$key]['logo'] = $value['logo'];
                            $pluginsData[$key]['use_auth_num'] = $use_auth_num;
                            $pluginsData[$key]['stock_auth_num'] = $value['auth_num'] - $use_auth_num;
                            $pluginsData[$key]['auth_num'] = $value['auth_num'];
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            return $this->fail($e->getMessage());
        }
        if (empty($pluginsData)) {
            $pluginsData = $defaultData;
        }
        $builder = new FormBuilder;
        $data = $builder
            ->setMethod('PUT')
            ->addRow('id', 'input', '站点编号', $model->id, [
                'disabled' => true,
                'col' => [
                    'span' => 12
                ],
            ])
            ->addRow('title', 'input', '站点名称', $model->title, [
                'disabled' => true,
                'col' => [
                    'span' => 12
                ],
            ])
            ->addComponent('plugins', 'remote', '授权应用', $pluginsData, [
                'col' => [
                    'span' => 24
                ],
                'props' => [
                    'file'                  => 'remote/app/auth',
                    'ajaxParams'            => [
                        'plugin_list'       => $plugins
                    ],
                ],
            ])
            ->create();
        return parent::successRes($data);
    }
    public function cannelAuth(Request $request)
    {
        $id = (int)$request->post('id');
        $storePlugins = StorePlugins::where(['id' => $id])->find();
        if (!$storePlugins) {
            return $this->fail('找不到该应用数据');
        }
        Db::startTrans();
        try {
            StorePlugins::where(['id' => $storePlugins->id])->delete();
            StorePluginsExpire::where(['store_plugins_id' => $storePlugins->id])->delete();
            Db::commit();
        } catch (\Throwable $th) {
            Db::rollback();
            return $this->fail($th->getMessage());
        }
        return $this->success('取消授权成功');
    }
    public function cannelSubAuth(Request $request)
    {
        $id = (int)$request->post('id');
        $storePluginsExpire = StorePluginsExpire::where(['id' => $id])->find();
        if (!$storePluginsExpire) {
            return $this->fail('找不到该应用数据');
        }
        Db::startTrans();
        try {
            StorePluginsExpire::where(['id' => $storePluginsExpire->id])->delete();
            $useAuthNum = StorePluginsExpire::where(['store_plugins_id' => $storePluginsExpire->store_plugins_id])->sum('auth_num');
            $storePlugins = StorePlugins::where(['id' => $storePluginsExpire->store_plugins_id])->find();
            $storePlugins->auth_num = $useAuthNum;
            $storePlugins->save();
            Db::commit();
        } catch (\Throwable $th) {
            Db::rollback();
            return $this->fail($th->getMessage());
        }
        return $this->success('取消授权成功');
    }
}
