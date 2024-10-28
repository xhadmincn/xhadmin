<?php

namespace app\store\service\develop;
use app\common\manager\PluginMgr;
use Exception;
use think\facade\Log;

/**
 * 应用插件安装
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
trait PluginInstall
{
    /**
     * 插件安装
     * @param array $data
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function pluginInstall(array $data)
    {
        try {
            # 获取安装类
            $installPath = root_path() . "plugin/{$this->teamPluginName}/api/Install.php";
            if (!file_exists($installPath)) {
                throw new Exception('安装类不存在');
            }
            # 引入安装类
            require_once $installPath;
            $installClass = "\\plugin\\{$this->teamPluginName}\\api\\Install";
            # 执行install安装
            if (class_exists($installClass) && method_exists($installClass, 'install')) {
                # 获取本地版本
                $local_version = PluginMgr::getPluginVersion($this->teamPluginName);
                # 执行数据安装
                call_user_func([$installClass, 'install'], $local_version);
            }
        } catch (\Throwable $e) {
            Log::write($e->getMessage(), 'plugin_install_dev_error');
            throw $e;
        }
    }
}