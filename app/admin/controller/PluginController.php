<?php

namespace app\admin\controller;

use app\common\builder\ListBuilder;
use app\common\enum\InstallEnum;
use app\common\enum\PlatformTypes;
use app\common\manager\PluginInstallMgr;
use app\common\manager\PluginMgr;
use app\common\manager\PluginUpdateMgr;
use app\common\model\StoreApp;
use app\common\service\SystemInfoService;
use app\common\BaseController;
use app\common\enum\PluginType;
use app\common\enum\PluginTypeStyle;
use app\common\utils\DirUtil;
use support\Request;
use YcOpen\CloudService\Cloud;
use YcOpen\CloudService\Request as CloudServiceRequest;
use YcOpen\CloudService\Request\PluginRequest;

/**
 * 插件管理
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-05-05
 */
class PluginController extends BaseController
{
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
                'width' => 230
            ])
            ->pageConfig()
            ->addTopButton(
                'cloud',
                '云服务',
                [
                    'type' => 'remote',
                    'api' => 'admin/PluginCloud/index',
                    'path' => 'remote/cloud/index'
                ],
                [
                    'title' => '云服务中心',
                    'width' => '350px'
                ],
                [
                    'type' => 'primary'
                ]
            )
            ->addRightButton(
                'doc',
                '文档',
                [
                    'type' => 'link',
                    'api' => 'admin/Plugin/getLink',
                    'queryParams' => [
                        'type' => 'doc'
                    ],
                    'aliasParams' => [
                        'name',
                        'version'
                    ],
                ],
                [],
                [
                    'type'      => 'warning',
                    'text'      => false,
                ]
            )
            ->addRightButton(
                'update',
                '更新',
                [
                    'type' => 'remote',
                    'api' => 'admin/Plugin/update',
                    'path' => 'remote/cloud/update',
                    'params' => [
                        'field' => 'updateed',
                        'value' => 'update',
                    ],
                    'aliasParams' => [
                        'name',
                        'version'
                    ],
                ],
                [
                    'title' => '更新应用',
                ],
                [
                    'type'      => 'success',
                    'text'      => false,
                ]
            )
            ->addRightButton(
                'bindsite',
                '去绑定',
                [
                    'type' => 'link',
                    'api' => 'admin/Plugin/getLink',
                    'params' => [
                        'field' => 'updateed',
                        'value' => 'bindsite',
                    ],
                    'queryParams' => [
                        'type' => 'bindsite'
                    ],
                    'aliasParams' => [
                        'name',
                        'version'
                    ],
                ],
                [],
                [
                    'type'      => 'info',
                    'text'      => false,
                ]
            )
            ->addRightButton(
                'install',
                '安装',
                [
                    'type' => 'remote',
                    'api' => 'admin/Plugin/install',
                    'path' => 'remote/cloud/install',
                    'params' => [
                        'field' => 'installed',
                        'value' => 'install',
                    ],
                    'aliasParams' => [
                        'name',
                        'version'
                    ],
                ],
                [
                    'title'     => '应用安装',
                ],
                [
                    'type'      => 'primary',
                ]
            )
            ->addRightButton(
                'uninstall',
                '卸载',
                [
                    'type' => 'confirm',
                    'api' => 'admin/Plugin/uninstall',
                    'path' => 'remote/cloud/uninstall',
                    'method' => 'delete',
                    'params' => [
                        'field' => 'installed',
                        'value' => 'uninstall',
                    ],
                    'aliasParams' => [
                        'name',
                        'version'
                    ],
                ],
                [
                    'type'=>'error',
                    'title' => '温馨提示',
                    'content' => "是否确认卸载该应用插件？\n该操作者将不可恢复数据，请自行备份应用数据",
                ],
                [
                    'type'      => 'danger',
                    'text'      => false,
                ]
            )
            ->tabsConfig([
                'active' => '0',
                'field' => 'active',
                'list' => [
                    [
                        'label' => '全部',
                        'value' => '0',
                    ],
                    [
                        'label' => '未安装',
                        'value' => '10',
                    ],
                    [
                        'label' => '已安装',
                        'value' => '20',
                    ],
                ]
            ])
            ->addColumn('title', '应用信息')
            ->addColumn('version_name', '最新版本', [
                'width' => 100
            ])
            ->addColumn('team_title', '开发者')
            ->addColumnEle('money', '价格', [
                'width' => 150,
                'params' => [
                    'type'      => 'money',
                    'style'     => [
                        'color' => 'red'
                    ],
                ]
            ])
            ->addColumnEle('logo', '图标', [
                'width' => 80,
                'params' => [
                    'type' => 'image',
                ],
            ])
            ->addColumnEle('platform_icon', '支持平台', [
                'params' => [
                    'type' => 'images',
                    'previewDisabled' => true
                ],
            ])
            ->addColumnEle('plugin_type', '应用类型', [
                'width' => 100,
                'params' => [
                    'type' => 'tags',
                    'options' => PluginType::dictOptions(),
                    'style' => PluginTypeStyle::parseAlias('type'),
                ],
            ])
            ->addColumn('min_version', '要求框架版本', [
                'width' => 120,
                'titlePrefix' => [
                    'content' => '该应用对于主框架的版本最低要求',
                ],
            ])
            ->create();
        return parent::successRes($data);
    }

    /**
     * 应用物料数据
     * @param \support\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function getPluginData(Request $request)
    {
        # 获取分类
        $cate = CloudServiceRequest::PluginCate()
            ->index()
            ->response()
            ->toArray();
        # 获取是否安装
        $installed = InstallEnum::getOptions();
        # 应用类型
        $pluginType = PlatformTypes::getOptions();
        $data       = [
            'category'      => $cate,
            'installed'     => $installed,
            'platforms'     => $pluginType,
        ];
        return $this->successRes($data);
    }

    /**
     * 列表
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-08
     */
    public function index(Request $request)
    {
        $page   = (int) $request->get('page', 1);
        $installed = $request->get('installed', '');
        $platform = $request->get('platform', '');
        $category = $request->get('category', '');
        $keyword = $request->get('keyword', '');

        $installData  = PluginMgr::getLocalPlugins();
        $systemInfo = SystemInfoService::info();
        $query      = [
            'title'         => $keyword,
            'cate_top_id'   => $category,
            'installed'     => $installed,
            'platform'      => $platform,
            'page'          => $page,
            'plugins'       => $installData,
            'limit'         =>  12,
            'saas_version'  => $systemInfo['system_version']
        ];
        $res        = CloudServiceRequest::Plugin()
            ->list($query)
            ->response();
        $data       = $res->toArray();
        foreach ($data['data'] as $key => $value) {
            $clientVersion = '';
            if (!empty($value['installed']) && $value['installed'] === 'uninstall') {
                $clientVersion = PluginMgr::getPluginVersion($value['name'], 'version_name');
            }
            $data['data'][$key]['title']       = "{$value['title']} {$clientVersion}";
            $data['data'][$key]['min_version'] = "无";
            if ($value['saas_version']) {
                $data['data'][$key]['min_version'] = $value['saas_version'];
            }
        }
        return $this->successRes($data);
    }

    /**
     * 获取文档地址（即将废弃）
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-06
     */
    public function getLink(Request $request)
    {
        $type    = $request->get('type', '');
        $name    = $request->get('name', '');
        $version = (int) $request->get('version', 0);
        $url     = '';
        switch ($type) {
            # 获取文档地址
            case 'doc':
                $systemInfo = SystemInfoService::info();
                $installed_version = PluginMgr::getPluginVersion($name);
                $req = new PluginRequest;
                $req->detail();
                $req->name = $name;
                $req->version = $version;
                $req->saas_version = $systemInfo['system_version'];
                $req->local_version = $installed_version;
                $cloud = new Cloud($req);
                $data = $cloud->send()->toArray();
                $url = $data['doc_url'];
                break;
            # 绑定站点
            case 'bindsite':
                $info = SystemInfoService::info();
                $url = isset($info['public_api']['bindsite']) ? $info['public_api']['bindsite'] : '';
                break;
        }
        return $this->successRes(['url' => $url]);
    }

    /**
     * 购买应用
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-08
     */
    public function buy(Request $request)
    {
        $name        = $request->post('name');
        $version     = $request->post('version');
        $coupon_code = $request->post('coupon_code');

        $systemInfo        = SystemInfoService::info();
        $installed_version = PluginMgr::getPluginVersion($name);
        $req               = new PluginRequest;
        $req->buy();
        $req->name          = $name;
        $req->version       = $version;
        $req->saas_version  = $systemInfo['system_version'];
        $req->local_version = $installed_version;
        $req->coupon_code   = $coupon_code;
        $cloud              = new Cloud($req);
        $cloud->send()->toArray();
        return $this->success('购买成功');
    }

    /**
     * 应用插件详情
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-08
     */
    public function detail(Request $request)
    {
        $name    = $request->get('name','');
        $version = $request->get('version','');

        $systemInfo           = SystemInfoService::info();
        $localVersion         = PluginMgr::getPluginVersion($name);
        $localVersionName     = PluginMgr::getPluginVersion($name,'version_name');
        $res                  = CloudServiceRequest::Plugin()
            ->detail([
                'name'          => $name,
                'version'       => $version,
                'saas_version'  => $systemInfo['system_version'],
                'local_version' => $localVersion
            ])
            ->response();
        $data                 = $res->toArray();
        $data['localVersion'] = $localVersion;
        $data['localVersionName'] = $localVersionName;
        return $this->successRes($data);
    }

    /**
     * 安装应用
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-08
     */
    public function install(Request $request)
    {
        $funcName = $request->get('step', '');
        $name     = $request->post('name', '');
        $version  = (int) $request->post('version', 0);
        if (empty($name)) {
            return $this->fail('应用名称参数错误');
        }
        if (empty($version)) {
            return $this->fail('目标版本参数错误');
        }
        if (empty($funcName)) {
            return $this->fail('操作方法出错');
        }
        try {
            $class = new PluginInstallMgr($request, $name, $version);
            return call_user_func([$class, $funcName]);
        } catch (\Throwable $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 更新
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-09
     */
    public function update(Request $request)
    {
        $funcName = $request->get('step', '');
        $name     = $request->post('name', '');
        $version  = (int) $request->post('version', 0);
        if (empty($name)) {
            return $this->fail('应用名称参数错误');
        }
        if (empty($version)) {
            return $this->fail('目标版本参数错误');
        }
        if (empty($funcName)) {
            return $this->fail('操作方法出错');
        }
        // 执行转发
        $class = new PluginUpdateMgr($request, $name, $version);
        return call_user_func([$class, $funcName]);
    }

    /**
     * 卸载应用
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-08
     */
    public function uninstall(Request $request)
    {
        $name    = $request->post('name');
        $version = $request->post('version');
        if (!$name || !preg_match('/^[a-zA-Z0-9_]+$/', $name)) {
            return $this->fail('参数错误');
        }
        # 获得插件路径
        $path = root_path()."plugin/{$name}";
        if (!is_dir($path)) {
            return $this->success('卸载成功');
        }
        # 检测是否存在项目
        if (StoreApp::where(['name'=> $name])->count()) {
            return $this->fail('该应用存在项目，请先删除');
        }
        # 执行uninstall卸载
        $install_class = "\\plugin\\{$name}\\api\\Install";
        if (class_exists($install_class) && method_exists($install_class, 'uninstall')) {
            call_user_func([$install_class, 'uninstall'], $version);
        }
        # 删除目录
        DirUtil::delDir($path);
        # 返回操作结果
        return $this->success('卸载成功');
    }
}