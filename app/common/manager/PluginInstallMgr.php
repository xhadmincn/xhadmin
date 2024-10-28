<?php
namespace app\common\manager;

use app\common\service\SystemInfoService;
use app\common\manager\JsonMgr;
use app\common\utils\DirUtil;
use Exception;
use support\Request;
use think\facade\Log;
use YcOpen\CloudService\Request as CloudServiceRequest;

/**
 * 应用安装管理器
 * @author 贵州猿创科技有限公司
 * @copyright (c) 贵州猿创科技有限公司
 */
class PluginInstallMgr
{
    /**
     * 请求对象
     * @var Request
     */
    protected $request = null;
    /**
     * 应用标识
     * @var string
     */
    protected $name = null;

    /**
     * 目标版本号
     * @var int
     */
    protected $version = null;

    /**
     * 应用路径
     * @var string
     */
    protected $pluginPath = null;

    /**
     * 下载包储存路径
     * @var string
     */
    protected $zipFile = null;

    /**
     * 构造函数
     * @param \support\Request $request
     * @param string $name
     * @param int $version
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function __construct(Request $request, string $name, int $version)
    {
        $this->request = $request;
        $this->name    = $name;
        $this->version = $version;
        # 应用路径
        $this->pluginPath = root_path() . "plugin/{$this->name}";
        # 下载包储存路径
        $this->zipFile = runtime_path() . "{$this->name}-install.zip";
    }

    /**
     * 下载安装包
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function download()
    {
        # 获取本地版本
        $installed_version = PluginMgr::getPluginVersion($this->name);
        if ($installed_version > 1) {
            throw new Exception('该应用插件已安装');
        }
        if (file_exists($this->pluginPath . "/api/Install.php")) {
            return JsonMgr::successFul('项目文件已存在，开始安装数据', [
                'next' => 'updateData',
            ]);
        }
        # 获取下载KEY
        $systemInfo = SystemInfoService::info();
        $res        = CloudServiceRequest::Plugin()->getKey()->setQuery([
            'name' => $this->name,
            'version' => $this->version,
            'saas_version' => $systemInfo['system_version'],
            'local_version' => $installed_version,
        ])->response();
        # 开始下载压缩包
        $request = new CloudServiceRequest();
        # 通过获取下载密钥接口获得
        $request->setUrl($res->url);
        # 保存文件到指定路径
        $status = $request->setSaveFile($this->zipFile)->cloud()->send();
        if (!$status) {
            throw new Exception('安装包下载失败');
        }
        return JsonMgr::successFul('下载成功', [
            'next' => 'unzip',
        ]);
    }

    /**
     * 解压安装包
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function unzip()
    {
        # 检测安装包是否存在
        if (!file_exists($this->zipFile)) {
            throw new Exception('安装包不存在');
        }
        # 解压安装包
        ZipMgr::unzip($this->zipFile, $this->pluginPath);
        # 解压成功，删除安装包
        file_exists($this->zipFile) && unlink($this->zipFile);
        # 操作完成
        return JsonMgr::successFul('解压成功', [
            'next' => 'updateData',
        ]);
    }

    /**
     * 安装数据
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function updateData()
    {
        try {
            # 获取安装类
            $installPath = root_path() . "plugin/{$this->name}/api/Install.php";
            if (!file_exists($installPath)) {
                throw new Exception('安装类不存在');
            }
            # 引入安装类
            require_once $installPath;
            $installClass = "\\plugin\\{$this->name}\\api\\Install";
            # 执行install安装
            if (class_exists($installClass) && method_exists($installClass, 'install')) {
                # 获取本地版本
                $local_version = PluginMgr::getPluginVersion($this->name);
                # 执行数据安装
                call_user_func([$installClass, 'install'], $local_version);
            }
        } catch (\Throwable $e) {
            # 安装失败，删除插件目录
            if (is_dir($this->pluginPath)) {
                DirUtil::delDir($this->pluginPath);
            }
            Log::write($e->getMessage(),'plugin_install_error');
            return JsonMgr::fail("安装数据失败");
        }
        return JsonMgr::successFul('安装数据成功', [
            'next' => 'success',
        ]);
    }

    /**
     * 安装成功
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function success()
    {
        return JsonMgr::successFul('应用插件安装成功', [
            'next' => '',
        ]);
    }
}