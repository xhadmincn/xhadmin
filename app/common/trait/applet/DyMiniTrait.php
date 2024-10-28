<?php
namespace app\common\trait\applet;

use app\common\manager\PluginMgr;
use app\common\manager\SettingsMgr;
use app\common\manager\StoreAppMgr;
use app\common\model\StoreApp;
use app\common\utils\Json;
use Exception;
use app\common\model\SystemConfig;
use support\Request;
use YcOpen\CloudService\Request as CloudServiceRequest;

/**
 * 抖音小程序接管类
 * @author 贵州猿创科技有限公司
 * @copyright (c) 贵州猿创科技有限公司
 */
trait DyMiniTrait
{
    # JSON
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
     * 抖音小程序显示页面
     * @param \support\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function douyin(Request $request)
    {
        $content = renderCustomView('remote/app/douyin');
        return $this->successRes($content);
    }

    /**
     * 获取/保存配置项
     * @param \support\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function douyinConfig(Request $request)
    {
        if (empty($this->saas_appid)) {
            throw new Exception('请设置托管的应用ID');
        }
        $model      = StoreAppMgr::model(['id' => $this->saas_appid]);
        $pluginPath = root_path("plugin/{$model['name']}");
        if (!is_dir($pluginPath)) {
            throw new Exception('该项目绑定应用已卸载');
        }
        $appletPath = "{$pluginPath}/config/douyin.php";
        if (!file_exists($appletPath)) {
            throw new Exception('该应用无抖音配置项文件');
        }
        # 检测应用对SAAS版本支持
        if (!PluginMgr::checkPluginSaasVersion($model['name'])) {
            throw new Exception('请先更新应用');
        }
        if ($request->isPut()) {
            # 获取配置数据
            $post = request()->post();
            $where    = [
                'saas_appid'        => $this->saas_appid,
                'group'             => 'douyin_config'
            ];
            $settingModel = SystemConfig::where($where)->find();
            if (!$settingModel) {
                $settingModel = new SystemConfig();
                $settingModel->saas_appid = $this->saas_appid;
                $settingModel->group = 'douyin_config';
            }
            $settingModel->value = $post;
            if (!$settingModel->save()) {
                throw new Exception('保存失败');
            }
            # 服务端小程序配置
            if (!empty($post['douyin_appid']) && !empty($post['douyin_secret']) && !empty($post['douyin_email']) && !empty($post['douyin_password'])) {
                $data = [
                    'appid'         => $post['douyin_appid'],
                    'secret'        => $post['douyin_secret'],
                    'email'         => $post['douyin_email'],
                    'password'      => $post['douyin_password'],
                    'type'          => 'douyin'
                ];
                CloudServiceRequest::Miniproject()
                    ->setConfig()
                    ->setParams($data)
                    ->cloud()
                    ->send();
            }
            # 返回数据
            return Json::success('保存成功');
        }
        # 返回配置项数据
        return $this->successRes([
            # 当前项目数据
            'app' => $model->toArray(),
            # 当前应用版本信息
            'version' => PluginMgr::getPluginVersionData($model['name']),
            # 当前项目配置信息
            'config' => SettingsMgr::group($this->saas_appid, 'douyin_config', []),
        ]);
    }

    /**
     * 小程序发布
     * @param \support\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function douyinPublish(Request $request)
    {
        if (empty($this->saas_appid)) {
            throw new Exception('必须设置托管的应用ID');
        }
        $model = StoreAppMgr::model(['id'=> $this->saas_appid]);
        # 检测应用对SAAS版本支持
        if (!PluginMgr::checkPluginSaasVersion($model['name'])) {
            throw new Exception('请先更新应用');
        }
        $applet_state = SettingsMgr::config($this->saas_appid,'douyin_config','douyin_state','');
        if (empty($applet_state)) {
            throw new Exception('请先配置抖音小程序发布参数');
        }
        $token      = $this->getToken($model);
        $tokenQuery = ['token' => $token];
        $projectCls = CloudServiceRequest::Miniproject();
        if ($applet_state === '20') {
            # 发布后二维码预览
            $data   = $projectCls
                ->miniprojectPreview()
                ->setQuery($tokenQuery)
                ->cloud()
                ->send();
            $data   = $data->toArray();
            $query  = [
                'appid'     => $this->saas_appid,
                'name'      => $model['name'],
            ];
            $qrcode = $projectCls
                ->setQuery($query)
                ->miniprojectPreviewQrcode();
            $data   = [
                'preview'       => true,
                'qrcode'        => $qrcode,
            ];
            return Json::successFul('发布成功', $data);
        } else {
            # 纯发布
            $projectCls
                ->miniprojectUpload()
                ->setQuery($tokenQuery)
                ->cloud()
                ->send();
            return Json::successFul('发布成功', [
                'preview' => false
            ]);
        }
    }
    
    /**
     * 获取令牌桶
     * @param \app\common\model\StoreApp $model
     * @return string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    private function getToken(StoreApp $model)
    {
        $config = SettingsMgr::group($this->saas_appid, 'douyin_config', []);
        if (empty($config)) {
            throw new Exception('请先配置系统');
        }
        if (empty($config['douyin_appid'])) {
            throw new Exception('请填写APPID');
        }
        if (empty($config['douyin_secret'])) {
            throw new Exception('请填写secret');
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
            'appid'             => $config['douyin_appid'],
            'name'              => $model['name'],
            'type'              => 'douyin',
            'siteinfo'          => [
                'name'          => $model['title'],
                'siteroot'      => $web_url,
                'app_id'        => $model['id'],
                'wx_appid'      => $config['douyin_appid'],                                      
            ]
        ];
        $data  = CloudServiceRequest::Miniproject()
            ->getUploadToken()
            ->setParams($query)
            ->cloud()
            ->send();
        return (string)$data['token'];
    }
}