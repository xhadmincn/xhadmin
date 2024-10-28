<?php

namespace app\store\service\develop;
use Exception;

/**
 * 开发者项目服务
 * @author 贵州猿创科技有限公司
 * @copyright (c) 2023
 */
trait CopyFiles
{
    /**
     * 插件模板路径
     * @var string|null
     */
    protected $pluginTplPath = null;

    /**
     * 插件目标路径
     * @var string|null
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $pluginPath = null;

    /**
     * 插件标识（带团队标识）
     * @var string|null
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $teamPluginName = null;

    /**
     * 插件标识（不带团队标识，开头大写）
     * @var string|null
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $pluginName = null;

    /**
     * 插件标识（不带团队标识，开头小写）
     * @var string|null
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $pluginComplete = null;

    /**
     * 复制应用文件
     * @param string $pluginPath
     * @param string $pluginName
     * @param array $data
     * @return void
     * @author John
     */
    protected function copyTplFile(array $data)
    {
        # 基础文件路径列表
        $files = [
            'api/Created.tpl',
            'api/Install.tpl',
            'api/Login.tpl',
            'api/database/install.sql',
            'api/database/update/remarks.txt',
            'app/BaseController.tpl',
            'app/common.tpl',
            'app/admin/controller/IndexController.tpl',
            'app/admin/controller/PublicsController.tpl',
            'app/admin/controller/SettingsController.tpl',
            'app/admin/controller/UploadCateController.tpl',
            'app/admin/controller/UploadController.tpl',
            'app/admin/middleware/AdminMiddleware.tpl',
            'app/controller/IndexController.tpl',
            'app/middleware/AuthMiddleware.tpl',
            'app/model/remarks.txt',
            'config/middleware.tpl',
            'config/settings.tpl',
            'config/settings/upload.tpl',
            'config/tabsconfig.tpl',
            'config/task.tpl',
            'config/advertisement.tpl',
            'package/remarks.txt',
            'public/remarks.txt',
            'public/remote/header-toolbar.vue',
            'public/remote/welcome.vue',
            'menus.json',
            'version.json',
        ];
        # 小程序管理器
        if (in_array('mini_wechat', $data['platform'])) {
            $files[] = 'app/admin/controller/AppletController.tpl';
            $files[] = 'config/applet.tpl';
        }
        # 文章系统
        if ($data['is_article'] == '20') {
            $files[] = 'app/admin/controller/ArticlesCateController.tpl';
            $files[] = 'app/admin/controller/ArticlesController.tpl';
        }
        # 单页系统
        if ($data['is_page'] == '20') {
            $files[] = 'app/admin/controller/TagsController.tpl';
        }
        # 广告系统
        if ($data['is_image'] == '20') {
            $files[] = 'app/admin/controller/AdsController.tpl';
        }
        # 基本设置
        if ($data['is_system'] === '20') {
            $files[] = 'config/settings/system.tpl';
        }
        # 权限管理
        if ($data['is_auth'] == '20') {
            $files[] = 'app/admin/controller/MenusController.tpl';
            $files[] = 'app/admin/controller/RolesController.tpl';
            $files[] = 'app/admin/controller/AdminController.tpl';
        }
        # 支付配置
        if ($data['is_pay'] == '20') {
            $files[] = 'config/tabs/pay.tpl';
        }
        # 流量主配置
        if ($data['is_ad'] == '20') {
            $files[] = 'config/tabs/advertisement.tpl';
        }
        # 短信配置
        if ($data['is_sms'] == '20') {
            $files[] = 'config/tabs/sms.tpl';
        }

        # 模板文件路径
        $pluginTplPath = $this->pluginTplPath;
        # 应用文件路径
        $pluginPath = $this->pluginPath;
        foreach ($files as $path) {
            # 检查模板文件是否存在
            $pluginTplFilePath = $pluginTplPath . $path;
            if (!file_exists($pluginTplFilePath)) {
                throw new Exception("模板文件【{$path}】不存在");
            }
            # 应用插件文件路径
            $filePath = $pluginPath . '/' . $path;
            $dirPath  = dirname($filePath);
            # 创建目录
            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0755, true);
            }
            # 复制文件
            copy($pluginTplFilePath, $filePath);
            # 替换文件内容
            $this->strReplaceTpl($filePath, $data);
        }
    }
    
    /**
     * 复制并文件
     * @param string $filePath
     * @param array $data
     * @throws \Exception
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private function strReplaceTpl(string $filePath, array $data)
    {
        if (!file_exists($filePath)) {
            throw new Exception("应用插件文件【{$filePath}】不存在");
        }
        # 替换文件内容
        $content = file_get_contents($filePath);
        $content = str_replace([
            '{TEAM_PLUGIN_NAME}',
            '{PLUGIN_NAME}',
            '{PLUGIN_COMPLETE_NAME}',
        ], [
            $this->teamPluginName,
            $this->pluginName,
            $this->pluginComplete,
        ], $content);
        # 替换内容并重写文件
        file_put_contents($filePath, $content);
        # 替换路径插件标识
        $targetFilePath = str_replace('PLUGIN_NAME_', $this->pluginName, $filePath);
        # 重命名文件后缀
        $phpFilePath    = str_replace('.tpl', '.php', $targetFilePath);
        # 重命名文件
        rename($filePath, $phpFilePath);
        # 获取文件信息
        $pathInfo = pathinfo($phpFilePath);
        if (isset($pathInfo['extension']) && $pathInfo['extension'] === 'php') {
            # 加载文件，防止类不存在
            require_once $phpFilePath;
        }
    }
}