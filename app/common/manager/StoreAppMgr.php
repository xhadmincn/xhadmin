<?php

namespace app\common\manager;

use app\admin\utils\UploadUtil;
use app\common\model\Store;
use app\common\model\StoreApp as modelStoreApp;
use app\common\model\StoreApp;
use app\common\model\SystemConfig;
use app\common\model\SystemUpload;
use app\common\model\SystemUploadCate;
use app\common\service\SystemInfoService;
use Exception;
use think\facade\Db;

class StoreAppMgr
{
    /**
     * 获取应用数据模型
     * @param array $where
     * @return \app\common\model\StoreApp
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function model(array $where): modelStoreApp
    {
        $model = modelStoreApp::where($where)->withTrashed()->find();
        if (!$model) {
            throw new Exception('找不到该项目数据');
        }
        return $model;
    }

    /**
     * 获取已购买应用并已安装的应用
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getBuyInstallApp()
    {
        $installed  = PluginMgr::getLocalPlugins();
        $systemInfo = SystemInfoService::info();
        $query      = [
            'installed'         => '20',
            'limit'             => 1000,
            'is_buy'            => '20',
            'plugins'           => $installed,
            'saas_version'      => $systemInfo['system_version']
        ];
        $res        = \YcOpen\CloudService\Request::Plugin()->list($query)->response();
        $data       = $res->data;
        return $data;
    }
    public static function getAppDetail($name)
    {
        $installed  = PluginMgr::getPluginVersionData($name);
        $systemInfo = SystemInfoService::info();
        $res                  = \YcOpen\CloudService\Request::Plugin()
            ->detail([
                'name'          => $name,
                'version'       => $installed['version'],
                'saas_version'  => $systemInfo['system_version'],
                'local_version' => $installed['version'],
            ])
            ->response();
        return $res->toArray();
    }

    /**
     * 获取已购买并已安装的应用选项列表
     * @return array<array>
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getBuyInstOptions()
    {
        # 获取已购买并已安装的应用
        $list = self::getBuyInstallApp();
        $data = [];
        foreach ($list as $key => $value) {
            $data[$key] = [
                'label'         => $value['title'],
                'value'         => $value['name']
            ];
        }
        return $data;
    }

    /**
     * 获取已授权的租户应用
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getAuthApp(int $store_id)
    {
        # 获取已购买并已安装的应用
        $list = self::getBuyInstallApp();
        # 获取租户已授权的应用
        $pluginsNames = Store::where('id', $store_id)->value('plugins_name');
        if (empty($pluginsNames)) {
            $pluginsNames = [];
        }
        $data = [];
        foreach ($list as $item) {
            if (!in_array($item['name'], $pluginsNames)) {
                continue;
            }
            $data[] = $item;
        }
        return $data;
    }

    /**
     * 获取已授权的租户应用详情
     * @param int $store_id
     * @param string $name
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getAuthAppDetail(int $store_id, string $name)
    {
        $list = self::getAuthApp($store_id);
        foreach ($list as $value) {
            if ($value['name'] == $name) {
                return $value;
            }
        }
        return null;
    }

    /**
     * 获取已授权的租户应用选项列表
     * @param int $store_id
     * @param bool $isLabel
     * @param bool $isPlatform
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getAuthAppOptions(int $store_id, bool $isLabel = true, bool $isPlatform = true)
    {
        # 获取已授权的租户应用
        $list = self::getAuthApp($store_id);
        $data = [];
        foreach ($list as $key => $value) {
            $title = $value['title'];
            if ($isLabel) {
                $title .= " {$value['version_name']}";
            }
            if ($isPlatform) {
                $platform = implode('、', $value['platform_text']);
                $title .= "【{$platform}】";
            }
            $data[$key] = [
                'label'         => $title,
                'value'         => $value['name'],
                'platform'      => $value['platform'],
            ];
        }
        return $data;
    }

    /**
     * 根据应用类型获取已授权的租户应用选项列表
     * @param int $store_id
     * @param string $platform
     * @param bool $isLabel
     * @return array<array>
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getAuthAppPlatformOptions(int $store_id, string $platform, bool $isLabel = true)
    {
        # 获取已授权的租户应用
        $list = self::getAuthApp($store_id);
        $data = [];
        foreach ($list as $key => $value) {
            if (!in_array($platform, $value['platform'])) {
                continue;
            }
            $title = $value['title'];
            if ($isLabel) {
                $title .= "（{$value['version_name']}）";
            }
            $data[$key] = [
                'label'         => $title,
                'value'         => $value['name'],
                'platform'      => $value['platform'],
            ];
        }
        return $data;
    }

    /**
     * 获取租户已创建应用的数量
     * @param int $store_id
     * @param string $platform
     * @return int
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getStoreCreatedPlatform(int $store_id, string $platform)
    {
        # 获取已创建应用的数量
        $where = [
            ['store_id', '=', $store_id],
            ['platform', 'like', '%"' . $platform . '"%']
        ];
        $storeAppNum = modelStoreApp::where($where)->count();
        return $storeAppNum;
    }

    /**
     * 获取项目数据
     * @param array $where
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function detail(array $where): array
    {
        return self::model($where)->toArray();
    }

    /**
     * 创建项目
     * @param array $data
     * @return bool
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function created(array $data)
    {
        if (empty($data['store_id'])) {
            throw new Exception('缺少参数 -- [站点ID]');
        }
        if (empty($data['platform'])) {
            throw new Exception('缺少参数 -- [项目类型]');
        }
        if (!is_array($data['platform'])) {
            $data['platform'] = [$data['platform']];
        }
        Db::startTrans();
        try {
            # 创建项目
            $model = new StoreApp;
            $model->save($data);
            # 检测并引入安装类
            $preatedPath = root_path() . "plugin/{$model['name']}/api/Created.php";
            if (!file_exists($preatedPath)) {
                throw new Exception("项目【{$model['name']}】安装类不存在");
            }
            require_once $preatedPath;
            # 执行项目插件方法
            $class = "\\plugin\\{$model['name']}\\api\\Created";
            if (method_exists($class, 'createAdmin')) {
                $data['id'] = $model->id;
                $logicCls = new $class;
                $logicCls->createAdmin($data);
            }
            # 创建附件库分类
            $cateData = [
                'saas_appid'        => $model->id,
                'title'             => '默认分类',
                'dir_name'          => $model['name'],
                'sort'              => 100,
                'is_system'         => '20'
            ];
            $uploadCateModel = new SystemUploadCate;
            if (!$uploadCateModel->save($cateData)) {
                throw new Exception('创建项目默认分类失败');
            }
            # 创建默认附件库配置
            $store        = StoreMgr::detail(['id' => $data['store_id']]);
            $request = request();
            $upload_drive = $store['is_uploadify'] === '20' ? 'local' : 'aliyun';
            $data = [
                'saas_appid'            => $model->id,
                'group'                 => 'upload',
                'value'                 => [
                    'upload_drive'      => $upload_drive,
                    'children'          => [
                        'local'         => [
                            'type'      => 'local',
                            'root'      => 'uploads',
                            'url'       => $request->domain()
                        ],
                        'aliyun'            => [
                            'type'          => 'aliyun',
                            'access_id'     => '',
                            'access_secret' => '',
                            'bucket'        => '',
                            'endpoint'      => '',
                            'isCName'       => true,
                        ]
                    ]
                ]
            ];
            $model        = new SystemConfig;
            if (!$model->save($data)) {
                throw new Exception('创建默认附件库配置失败');
            }
            # 提交事务
            Db::commit();
            # 创建成功
            return true;
        } catch (\Throwable $e) {
            Db::rollback();
            throw $e;
        }
    }

    /**
     * 删除项目
     * @param int $saas_appid
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function del(int $saas_appid)
    {
        if (!$saas_appid) {
            throw new Exception('参数错误--[项目ID]');
        }
        # 获取项目
        $model              = self::model(['id' => $saas_appid]);
        # 开始事务
        Db::startTrans();
        try {
            $where          = [
                'saas_appid'    => $model['id'],
            ];
            # 删除项目配置
            SystemConfig::where($where)->delete();
            # 删除项目附件
            SystemUpload::where($where)->delete();
            # 删除附件库分类
            SystemUploadCate::where($where)->delete();
            # 删除管理员
            Db::name('plugin_admin')->where($where)->delete();
            # 删除角色内容
            Db::name('plugin_roles')->where($where)->delete();
            # 删除广告内容
            Db::name('plugin_ads')->where($where)->delete();
            # 删除文章分类
            Db::name('plugin_articles_cate')->where($where)->delete();
            # 删除文章内容
            Db::name('plugin_articles')->where($where)->delete();
            # 删除单页内容
            Db::name('plugin_tags')->where($where)->delete();
            # 删除项目
            if (!$model->force()->delete()) {
                throw new Exception('删除项目失败');
            }
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            throw $e;
        }
    }
}
