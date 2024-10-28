<?php
namespace app\common\trait\applet;

use app\common\manager\AppletMgr;
use app\common\manager\PluginMgr;
use app\common\manager\StoreAppMgr;
use app\common\utils\Json;
use Exception;
use support\Request;

/**
 * 微信小程序接管类
 * @author 贵州猿创科技有限公司
 * @copyright (c) 贵州猿创科技有限公司
 */
trait WxMiniTrait
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
     * 显示小程序配置/上传页面
     * @param \support\Request $request
     * @return \think\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function index(Request $request)
    {
        $content = renderCustomView('remote/app/applet');
        return $this->successRes($content);
    }

    /**
     * 小程序配置获取/保存
     * @param \support\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function config(Request $request)
    {
        if (empty($this->saas_appid)) {
            throw new Exception('请设置托管的应用ID');
        }
        $model = StoreAppMgr::model(['id'=> $this->saas_appid]);
        $pluginPath = root_path("plugin/{$model->name}");
        if (!is_dir($pluginPath)) {
            throw new Exception('该项目绑定应用已卸载');
        }
        $appletPath = "{$pluginPath}/config/applet.php";
        if (!file_exists($appletPath)) {
            throw new Exception('请先创建并配置applet');
        }
        # 检测应用对SAAS版本支持
        if (!PluginMgr::checkPluginSaasVersion($model['name'])) {
            throw new Exception('请先更新应用');
        }
        $class = new AppletMgr($request, $model);
        $methodFun = 'getSettings';
        if ($request->method() === 'PUT') {
            $methodFun = 'saveData';
        }
        return call_user_func([$class, $methodFun]);
    }

    /**
     * 小程序发布
     * @param \support\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function publish(Request $request)
    {
        if (empty($this->saas_appid)) {
            throw new Exception('必须设置托管的应用ID');
        }
        $model = StoreAppMgr::model(['id'=> $this->saas_appid]);
        # 检测应用对SAAS版本支持
        if (!PluginMgr::checkPluginSaasVersion($model['name'])) {
            throw new Exception('请先更新应用');
        }
        $applet_state = getHpConfig('applet_state',$this->saas_appid,'applet');
        if (empty($applet_state)) {
            throw new Exception('请先配置小程序发布参数');
        }
        $class = new AppletMgr($request, $model);
        return $class->publish($this->saas_appid, $applet_state);
    }
}