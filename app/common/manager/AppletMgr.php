<?php
namespace app\common\manager;

use app\common\model\StoreApp;
use app\common\model\SystemConfig;
use Exception;
use support\Request;
use YcOpen\CloudService\Request as CloudServiceRequest;

/**
 * 小程序管理类
 * @author 贵州猿创科技有限公司
 * @copyright (c) 贵州猿创科技有限公司
 */
class AppletMgr
{
    /**
     * 请求对象
     * @var Request
     */
    protected $request = null;

    /**
     * 项目模型
     * @var StoreApp
     */
    protected $model;

    /**
     * 配置名称
     * @var array
     */
    protected $configName = [
        'applet_appid',
        'applet_secret',
        'applet_privatekey',
        'applet_state',
    ];

    /**
     * 构造函数
     * @param \support\Request $request
     * @param \app\common\model\StoreApp $storeApp
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function __construct(Request $request, StoreApp $storeApp)
    {
        $this->request = $request;
        $this->model   = $storeApp;
    }

    /**
     * 小程序发布
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function publish(int $appid,string $isPreview = '10')
    {
        $token      = $this->getToken();
        $tokenQuery = ['token' => $token];
        $projectCls = CloudServiceRequest::Miniproject();
        if ($isPreview === '20') {
            # 发布后二维码预览
            $data   = $projectCls
                ->miniprojectPreview()
                ->setQuery($tokenQuery)
                ->cloud()
                ->send();
            $data   = $data->toArray();
            $query  = [
                'appid'     => $appid,
                'name'      => $this->model['name'],
            ];
            $qrcode = $projectCls
                ->setQuery($query)
                ->miniprojectPreviewQrcode();
            $data   = [
                'preview'       => true,
                'qrcode'        => $qrcode,
            ];
            return JsonMgr::successFul('发布成功', $data);
        } else {
            # 纯发布
            $projectCls
                ->miniprojectUpload()
                ->setQuery($tokenQuery)
                ->cloud()
                ->send();
            return JsonMgr::successFul('发布成功', [
                'preview' => false
            ]);
        }
    }

    /**
     * 获取令牌桶
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private function getToken()
    {
        $model   = $this->model;
        $config = getHpConfig('',(int)$model['id'],'applet');
        if (empty($config)) {
            throw new Exception('请先配置系统');
        }
        if (empty($config['applet_appid'])) {
            throw new Exception('请填写APPID');
        }
        if (empty($config['applet_secret'])) {
            throw new Exception('请填写secret');
        }
        if (empty($config['applet_privatekey'])) {
            throw new Exception('请填写privatekey');
        }
        if (empty($model['title'])) {
            throw new Exception('请填写项目名称');
        }
        if (empty($model['name'])) {
            throw new Exception('项目绑定应用错误');
        }
        $pluginPath = root_path() . "/plugin/{$model['name']}";
        if (!is_dir($pluginPath)) {
            throw new Exception('项目绑定的应用未安装');
        }
        $web_url = getHpConfig('web_url', '', 'system');
        if (empty($web_url)) {
            throw new Exception('请先配置网站域名');
        }
        # 判断链接是否https
        if (strpos($web_url, 'https://') === false) {
            throw new Exception('网站域名必须为https');
        }
        $query = [
            'appid'             => $config['applet_appid'],
            'name'              => $model['name'],
            'preview_desc'      => '',
            'type'              => 'wxmp',
            'siteinfo'          => [
                'name'          => $model['title'],
                'siteroot'      => $web_url,
                'app_id'        => $model['id'],
                'wx_appid'      => $config['applet_appid'],                                      
            ]
        ];
        $data  = CloudServiceRequest::Miniproject()
            ->getUploadToken()
            ->setParams($query)
            ->cloud()
            ->send();
        return $data['token'];
    }

    
    /**
     * 获取小程序配置
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function getSettings()
    {
        $settingPath = root_path()."plugin/{$this->model['name']}/config/applet.php";
        if (!file_exists($settingPath)) {
            throw new Exception('请先创建并配置config/applet');
        }
        $where    = [
            'saas_appid'        => $this->model['id'],
            'group'             => 'applet'
        ];
        $settings = SystemConfig::where($where)->value('value');
        if (empty($settings)) {
            $settings = config("plugin.{$this->model['name']}.applet");
        }
        # 获取版本信息
        $versionPath = root_path()."plugin/{$this->model['name']}/version.json";
        if (!file_exists($versionPath)) {
            throw new Exception('获取应用版本信息失败');
        }
        $version = file_get_contents($versionPath);
        $version = json_decode($version, true);
        # 返回数据
        $modelData = $this->model->toArray();
        $data      = [
            'config'            => $settings,
            'app'               => $modelData,
            'version'           => $version,
        ];
        # 返回视图
        return JsonMgr::successRes($data);
    }

    /**
     * 保存项目系统配置
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function saveData()
    {
        # 获取配置数据
        $post = request()->post();
        # 验证数据
        $where    = [
            'saas_appid'        => $this->model['id'],
            'group'             => 'applet'
        ];
        $settingModel = SystemConfig::where($where)->find();
        if (empty($settingModel)) {
            $settingModel = new SystemConfig();
            $settingModel->saas_appid = $this->model['id'];
            $settingModel->store_id = $this->model['store_id'];
            $settingModel->group = 'applet';
        }
        if (is_bool($post['applet_state'])) {
            $post['applet_state'] = $post['applet_state'] ? '20' : '10';
        }
        $settingModel->value = $post;
        if (!$settingModel->save()) {
            throw new Exception('保存失败');
        }
        # 服务端小程序配置
        if (!empty($post['applet_appid']) && !empty($post['applet_secret']) && !empty($post['applet_privatekey'])) {
            $data = [
                'appid'         => $post['applet_appid'],
                'secret'        => $post['applet_secret'],
                'privatekey'    => $post['applet_privatekey'],
                'type'          => 'wxmp'
            ];
            CloudServiceRequest::Miniproject()
                ->setConfig()
                ->setParams($data)
                ->cloud()
                ->send();
        }
        # 返回数据
        return JsonMgr::success('保存成功');
    }

    /**
     * 小程序详情
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function detail()
    {
        $model          = $this->model;
        $data           = $model->toArray();
        $data['config'] = $this->getConfig();
        return JsonMgr::successRes($data);
    }

    /**
     * 获取小程序配置
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    private function getConfig()
    {
        $data  = getHpConfig('', $this->model['id'],'applet');
        return empty($data) ? [] : $data;
    }
}