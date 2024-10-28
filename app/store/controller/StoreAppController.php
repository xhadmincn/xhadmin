<?php

namespace app\store\controller;

use app\common\builder\FormBuilder;
use app\common\enum\StatusEnum;
use app\common\exception\RedirectException;
use app\common\manager\StoreAppMgr;
use app\common\model\Store;
use app\common\model\StorePlugins;
use app\common\model\StorePluginsExpire;
use app\common\BaseController;
use app\common\manager\PluginMgr;
use app\store\validate\StoreApp;
use Exception;
use think\facade\Log;
use support\Request;
use think\facade\Db;

/**
 * 应用管理
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-05-03
 */
class StoreAppController extends BaseController
{
    /**
     * 模型
     * @var \app\common\model\StoreApp
     */
    public $model;

    /**
     * 构造函数
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\store\model\StoreApp;
    }

    /**
     * 列表
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-01
     */
    public function index(Request $request)
    {
        $limit        = $request->get('limit', 12);
        $platformType = $request->get('platform', '');
        $model        = $this->model;
        $where        = [];
        $Store = Store::where(['id' => $request->user['id']])->find();
        $data         = $model
            ->with('store')
            ->withSearch(['platform'], ['platform' => $platformType])
            ->where($where)
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
                // 执行应用插件方法
                $class = "\\plugin\\{$item['name']}\\api\\Created";
                if (method_exists($class, 'read')) {
                    $post['id'] = $item->id;
                    $logicCls   = new $class;
                    try {
                        $userData = $logicCls->read($item->id);
                        isset($userData['username']) && $item->username = $userData['username'];
                    } catch (\Throwable $e) {
                    }
                }
            });
        return parent::successRes($data);
    }

    /**
     * 添加应用
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-11
     */
    public function create(Request $request)
    {
        # 获取登录租户信息
        $store = $request->user;
        if ($request->method() === 'POST') {
            # 获取数据
            $post = $request->post();

            # 重组数据
            $post['store_id'] = $store['id'];
            $post['status']   = '20';

            # 数据验证
            hpValidate(StoreApp::class, $post, 'add');
            $StorePluginsExpire = StorePluginsExpire::where(['id' => $post['auth_id']])->whereTime('expire_time', '>', time())->find();
            if (!$StorePluginsExpire) {
                return $this->fail('授权不存在或已过期');
            }
            $use_auth_num = $this->model->where(['store_id' => $store['id'], 'auth_id' => $StorePluginsExpire->id])->count();
            if ($use_auth_num >= $StorePluginsExpire->auth_num) {
                return $this->fail('授权数量已用完');
            }
            $StorePlugins = StorePlugins::where(['id' => $StorePluginsExpire->store_plugins_id])->find();
            $storeApp = StoreAppMgr::getAppDetail($StorePlugins->plugin_name);
            # 取平台类型
            $platforms = $storeApp['platform'] ?? [];

            /* # 验证平台数量是否充足
            foreach ($platforms as $value) {
                # 验证平台数量是否充足
                $storePlatformNum     = Store::where('id', $store['id'])->value($value);
                $storeCreatedPlatform = StoreAppMgr::getStoreCreatedPlatform((int) $store['id'], $value);
                if ($storeCreatedPlatform >= $storePlatformNum) {
                    throw new Exception("平台【{$value}】可用数量不足");
                }
            } */
            $post['platform'] = $platforms;
            $post['name']     = $StorePlugins->plugin_name;
            $post['auth_id']  = $StorePluginsExpire->id;
            # 创建项目
            StoreAppMgr::created($post);

            # 创建成功
            return $this->success('项目创建成功');
        }
        try {
            // $platformList = StoreAppMgr::getAuthAppOptions($store['id']);
            $platformList = [];
            $StorePluginsExpire = StorePluginsExpire::alias('sub')->where(['sub.store_id' => $store['id']])
                ->whereTime('sub.expire_time', '>', time())
                ->join('StorePlugins p', 'p.id=sub.store_plugins_id')
                ->field('sub.id,p.plugin_name,p.plugin_title,sub.expire_time,sub.auth_num')
                ->order('sub.expire_time asc,sub.id desc')
                ->select();
            foreach ($StorePluginsExpire as $key => $value) {
                $auth_num = $value['auth_num'];
                $use_auth_num = $this->model->where(['store_id' => $store['id'], 'auth_id' => $value['id']])->count();
                $stock_auth_num = $auth_num - $use_auth_num;
                $platformList[] = [
                    'value' => $value['id'],
                    'label' => "{$value['plugin_title']},有效期至:{$value['expire_time']},可用授权数量:{$stock_auth_num}",
                    'disabled' => $use_auth_num >= $auth_num ? true : false
                ];
            }
        } catch (\Throwable $e) {
            throw new RedirectException($e->getMessage(), "/#/Index/index");
        }
        $builder = new FormBuilder;
        $builder->setMethod('POST');
        $builder->addRow('title', 'input', '项目名称', '', [
            'col' => 12,
        ]);
        $builder->addRow('auth_id', 'select', '授权应用', '', [
            'col' => 12,
            'noDataText' => '您还没有更多的已授权应用',
            'options' => $platformList
        ]);
        $builder->addRow('username', 'input', '超管账号', '', [
            'col' => 12,
        ]);
        $builder->addRow('password', 'input', '登录密码', '', [
            'col' => 12,
        ]);
        $builder->addComponent('logo', 'uploadify', '项目图标', '', [
            'col' => 12,
            'props' => [
                'type' => 'image',
                'format' => ['jpg', 'jpeg', 'png']
            ],
        ]);
        $data = $builder->create();
        return parent::successRes($data);
    }

    /**
     * 修改应用
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-12
     */
    public function edit(Request $request)
    {
        $store_id = $request->user['id'];
        $app_id   = $request->get('id', '');
        $model    = $this->model;
        $where    = [
            ['id', '=', $app_id],
        ];
        $model    = $model->where($where)->find();
        if (!$model) {
            return $this->fail('该应用不存在');
        }
        # 检测应用对SAAS版本支持
        try {
            if (!PluginMgr::checkPluginSaasVersion($model['name'])) {
                throw new Exception('请先更新应用');
            }
        } catch (\Throwable $e) {
            throw new RedirectException($e->getMessage(), "/#/Index/index");
        }
        if ($request->method() === 'PUT') {
            $post             = $request->post();
            $post['store_id'] = $store_id;

            hpValidate(StoreApp::class, $post, 'edit');

            Db::startTrans();
            try {
                $StorePluginsExpire = StorePluginsExpire::where(['id' => $post['auth_id']])->whereTime('expire_time', '>', time())->find();
                if (!$StorePluginsExpire) {
                    return $this->fail('授权不存在或已过期');
                }
                if ($StorePluginsExpire->id !== $model->auth_id) {
                    $use_auth_num = $this->model->where(['store_id' => $store_id, 'auth_id' => $StorePluginsExpire->id])->count();
                    if ($use_auth_num >= $StorePluginsExpire->auth_num) {
                        return $this->fail('授权数量已用完');
                    }
                }
                # 修改项目
                $model->save($post);
                // 执行应用插件方法
                $class = "\\plugin\\{$model->name}\\api\\Created";
                if (method_exists($class, 'createAdmin')) {
                    $post['id'] = $model->id;
                    $logicCls   = new $class;
                    $logicCls->createAdmin($post);
                }
                Db::commit();
                return $this->success('操作成功');
            } catch (\Throwable $e) {
                Db::rollback();
                return $this->fail($e->getMessage());
            }
        }
        $formData = $model->toArray();
        # 检测是否空平台
        $formData['platform'] = array_filter($formData['platform']);
        if (empty($formData['platform'])) {
            $appDetail = StoreAppMgr::getAppDetail($formData['name']);
            $formData['platform'] = $appDetail['platform'];
        }
        // 执行应用插件方法
        $class = "\\plugin\\{$model['name']}\\api\\Created";
        if (method_exists($class, 'read')) {
            $post['id'] = $model->id;
            $logicCls   = new $class;
            try {
                $userData = $logicCls->read($model->id);
                isset($userData['username']) && $formData['username'] = $userData['username'];
            } catch (\Throwable $e) {
                Log::error("获取管理员数据出错：{$e->getMessage()}");
                throw $e;
            }
        }
        try {
            // $platformList = StoreAppMgr::getAuthAppOptions($store['id']);
            $platformList = [];
            $StorePluginsExpire = StorePluginsExpire::alias('sub')->where(['sub.store_id' => $formData['store_id'], 'p.plugin_name' => $formData['name']])
                ->whereTime('sub.expire_time', '>', time())
                ->join('StorePlugins p', 'p.id=sub.store_plugins_id')
                ->field('sub.id,p.plugin_name,p.plugin_title,sub.expire_time,sub.auth_num')
                ->order('sub.expire_time asc,sub.id desc')
                ->select();
            foreach ($StorePluginsExpire as $key => $value) {
                $auth_num = $value['auth_num'];
                $use_auth_num = $this->model->where(['store_id' => $formData['store_id'], 'auth_id' => $value['id']])->count();
                $stock_auth_num = $auth_num - $use_auth_num;
                $platformList[] = [
                    'value' => $value['id'],
                    'label' => "{$value['plugin_title']},有效期至:{$value['expire_time']},可用授权数量:{$stock_auth_num}",
                    'disabled' => $use_auth_num >= $auth_num ? true : false
                ];
            }
        } catch (\Throwable $e) {
            throw new RedirectException($e->getMessage(), "/#/Index/index");
        }
        $builder = new FormBuilder;
        $builder->setMethod('PUT')
            ->addRow('title', 'input', '项目名称', '', [
                'col' => 12,
            ])
            ->addRow('status', 'radio', '项目状态', '10', [
                'col' => 6,
                'options' => StatusEnum::getOptions()
            ])
            ->addComponent('platform', 'info', '项目类型', '', [
                'col' => 12,
            ]);

        $builder->addRow('auth_id', 'select', '授权应用', '', [
            'col' => 12,
            'noDataText' => '您还没有更多的已授权应用',
            'options' => $platformList
        ]);
        $builder->addRow('username', 'input', '超管账号', '', [
            'col' => 12,
        ])
            ->addRow('password', 'input', '登录密码', '', [
                'col' => 12,
            ])
            ->addComponent('logo', 'uploadify', '项目图标', '', [
                'col' => 12,
                'props' => [
                    'type' => 'image',
                    'format' => ['jpg', 'jpeg', 'png']
                ],
            ]);
        $builder->setFormData($formData);
        $data = $builder->create();
        return parent::successRes($data);
    }

    /**
     * 删除项目
     * @param \support\Request $request
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function del(Request $request)
    {
        # 获取数据
        $saas_appid = (int) $request->post('id', 0);
        $store_id   = $request->user['id'];
        $storeApp   = $this->model->where(['id' => $saas_appid, 'store_id' => $store_id])->find();
        if (!$storeApp) {
            return $this->fail('找不到该应用');
        }
        # 删除项目
        if (!$storeApp->delete()) {
            return $this->fail('操作失败');
        }
        return $this->success('操作成功');
    }

    /**
     * 登录项目数据管理
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-12
     */
    public function login(Request $request)
    {
        $app_id = $request->post('appid_id', '');
        $model  = $this->model;

        $where = [
            'id' => $app_id
        ];
        $model = $model->where($where)->find();
        if (!$model) {
            return $this->fail('找不到该应用');
        }
        # 检测应用是否存在
        if (!is_dir(root_path() . "plugin/{$model->name}")) {
            return $this->fail('该项目绑定应用已卸载');
        }
        # 检测应用对SAAS版本支持
        if (!PluginMgr::checkPluginSaasVersion($model['name'])) {
            throw new RedirectException('请先更新应用', "/#/Index/index");
        }
        try {
            $class = "\\plugin\\{$model->name}\\api\\Login";
            if (!class_exists($class)) {
                throw new Exception("该应用插件 [{$model['name']}] 未实现登录类");
            }
            if (!method_exists($class, 'login')) {
                throw new Exception("该应用插件 [{$model['name']}] 未实现登录方法");
            }
            $logicCls = new $class;
            $response = $logicCls->login($request, (int) $model->id);
            if (!isset($response['url'])) {
                throw new Exception('应用插件没有提供登录地址');
            }
            $data = [
                'url' => $response['url']
            ];
            return $this->successRes($data);
        } catch (\Throwable $e) {
            return $this->fail($e->getMessage());
        }
    }
}
