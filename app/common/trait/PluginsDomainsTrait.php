<?php
namespace app\common\trait;

use app\common\manager\SettingsMgr;
use app\common\model\SystemConfig;
use app\common\model\SystemPluginsDomains;
use app\common\utils\Json;
use think\facade\Db;
use Exception;
use support\Request;

trait PluginsDomainsTrait
{
    # 使用JSON工具类
    use Json;

    /**
     * 请求对象
     * @var Request
     */
    protected $request = null;

    /**
     * 应用ID（null则获取系统配置）
     * @var string|null
     */
    protected $saas_appid = null;

    /**
     * 获取配置项
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function settings(Request $request)
    {
        # 不存在则为系统配置
        $isSystem = (int) $this->saas_appid;
        # 获取配置项分组
        $group = 'plugins_domains';
        # 获取应用插件配置项
        $plugin   = $request->plugin;
        if ($request->isPost()) {
            $post  = $request->post();
            Db::startTrans();
            try {
                $where = [
                    'group' => $group,
                    'saas_appid' => $this->saas_appid,
                ];
                $model = SystemConfig::where($where)->find();
                if (!$model) {
                    $model             = new SystemConfig;
                    $model->group      = $group;
                    $model->saas_appid = $this->saas_appid;
                }
                $model->value = $post;
                $model->save();
                SystemPluginsDomains::where('saas_appid', $this->saas_appid)->delete();
                $web_url=getHpConfig('web_url');
                $web_url_host=parse_url($web_url,PHP_URL_HOST);
                if(!empty($post['domain'])){
                    # 获取$post['domain']中的domain
                    $host=parse_url($post['domain'],PHP_URL_HOST);
                    if(!$host){
                        throw new Exception("{$post['domain']}域名解析失败");
                    }
                    if($web_url_host!=$host){
                        if(SystemPluginsDomains::where('host',$host)->find()){
                            throw new Exception("{$host}域名已存在");
                        }
                        $SystemPluginsDomains=new SystemPluginsDomains;
                        $SystemPluginsDomains->save([
                            'saas_appid'=>$this->saas_appid,
                            'domain'=>$post['domain'],
                            'host'=>$host,
                            'plugin'=>$plugin,
                        ]);
                    }
                    if (!empty($post['sub_domain'])) {
                        $data=[];
                        foreach ($post['sub_domain'] as $key => $value) {
                            $host=parse_url($value,PHP_URL_HOST);
                            if(!$host){
                                throw new Exception("{$value}域名解析失败");
                            }
                            if($web_url_host!=$host){
                                if(SystemPluginsDomains::where('host',$host)->find()){
                                    throw new Exception("{$host}域名已存在");
                                }
                                $data[]=[
                                    'saas_appid'=>$this->saas_appid,
                                    'domain'=>$value,
                                    'plugin'=>$plugin,
                                    'host'=>$host,
                                ];
                            }
                        }
                        if(!empty($data)){
                            $SystemPluginsDomains=new SystemPluginsDomains;
                            $SystemPluginsDomains->saveAll($data);
                        }
                    }
                }
                Db::commit();
            } catch (\Throwable $th) {
                Db::rollback();
                return $this->fail($th->getMessage());
            }
            $this->writeConfig();
            return $this->success('保存成功');
        } else {
            # 查询配置数据
            $where    = [
                'group' => $group,
                'saas_appid' => $this->saas_appid
            ];
            $formData = SettingsMgr::getOriginConfig($where, []);
            $settings = [
                'domain'=>$formData['domain'] ?? '',
                'sub_domain'=>$formData['sub_domain'] ?? [],
            ];
            return Json::successRes($settings);
        }
    }
    public function writeConfig()
    {
        $model = SystemPluginsDomains::select();
        $data = [];
        foreach ($model as $item) {
            $data[$item->host]=[
                'plugin'=>$item->plugin,
                'appid'=>$item->saas_appid,
            ];
        }
        $path = root_path('config') . 'yc_plugins_domains.php';
        file_put_contents($path, "<?php\nreturn " . var_export($data, true) . ";");
    }
}