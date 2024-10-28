<?php

namespace app\store\service\develop;
use app\common\manager\PluginMgr;
use app\common\utils\Data;
use Exception;

/**
 * 数据处理
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
trait DataCheck
{
    /**
     * 临时合并菜单数据
     * @var array
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private $mergeMenuData = [];

    /**
     * 额外处理
     * @param array $data
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function checkMenuData(array $data)
    {
        # 目标菜单文件
        $menuPath = $this->pluginPath . '/menus.json';
        if (!file_exists($menuPath)) {
            throw new Exception("应用插件菜单文件不存在");
        }
        # 模板菜单文件
        $menuTplPath = $this->pluginTplPath . '/menus.json';
        if (!file_exists($menuTplPath)) {
            throw new Exception("应用插件菜单模板文件不存在");
        }
        # 默认菜单数据
        $menuData = file_get_contents($menuTplPath);
        # 替换文件内容
        $menuData = str_replace([
            '{TEAM_PLUGIN_NAME}',
            '{PLUGIN_NAME}',
            '{PLUGIN_COMPLETE_NAME}',
        ], [
            $this->teamPluginName,
            $this->pluginName,
            $this->pluginComplete,
        ], $menuData);
        # 转换菜单数据为数组
        $menuData = json_decode($menuData, true);
        # 转换多层菜单为二维数组
        $menuData = PluginMgr::parseMenus($menuData);
        # 配置项菜单
        $mergeMenuData = $this->mergeSettingsMenus($menuData,$data);
        # 内容
        if ($data['is_article'] == '20') {
            $mergeMenuData = $this->mergeContentMenus($mergeMenuData,$data);
        }
        # 平台
        $mergeMenuData = $this->mergePlatformMenus($mergeMenuData,$data);
        # 转换数据格式并写入最终菜单
        $this->writeMenus($menuPath,$mergeMenuData);
    }

    /**
     * 合并配置项菜单
     * @param string $filePath
     * @param array $mergeMenuData
     * @param array $data
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private function mergeSettingsMenus(array $mergeMenuData,array $data)
    {
        # 基本设置
        $systemMenuPath = $this->pluginTplPath . "menus/system.json";
        if ($data['is_system'] === '20' && file_exists($systemMenuPath)) {
            $menuData = file_get_contents($systemMenuPath);
            $menuData = json_decode($menuData, true);
            $menuData = PluginMgr::parseMenus($menuData);
            $mergeMenuData = array_merge($mergeMenuData,$menuData);
        }
        # 附件库设置
        $uploadMenuPath = $this->pluginTplPath . "menus/upload.json";
        if (file_exists($uploadMenuPath)) {
            $menuData = file_get_contents($uploadMenuPath);
            $menuData = json_decode($menuData, true);
            $menuData = PluginMgr::parseMenus($menuData);
            $mergeMenuData = array_merge($mergeMenuData,$menuData);
        }
        # 支付设置
        $payMenuPath = $this->pluginTplPath . "menus/tabs/pay.json";
        if ($data['is_pay'] === '20' && file_exists($payMenuPath)) {
            $menuData = file_get_contents($payMenuPath);
            $menuData = json_decode($menuData, true);
            $menuData = PluginMgr::parseMenus($menuData);
            $mergeMenuData = array_merge($mergeMenuData,$menuData);
        }
        # 流量主设置
        $adMenuPath = $this->pluginTplPath . "menus/tabs/advertisement.json";
        if ($data['is_ad'] === '20' && file_exists($adMenuPath)) {
            $menuData = file_get_contents($adMenuPath);
            $menuData = json_decode($menuData, true);
            $menuData = PluginMgr::parseMenus($menuData);
            $mergeMenuData = array_merge($mergeMenuData,$menuData);
        }
        # 短信设置
        $smsMenuPath = $this->pluginTplPath . "menus/tabs/sms.json";
        if ($data['is_sms'] === '20' && file_exists($smsMenuPath)) {
            $menuData = file_get_contents($smsMenuPath);
            $menuData = json_decode($menuData, true);
            $menuData = PluginMgr::parseMenus($menuData);
            $mergeMenuData = array_merge($mergeMenuData,$menuData);
        }

        # 返回菜单
        return $mergeMenuData;
    }

    /**
     * 合并内容菜单
     * @param string $filePath
     * @param array $mergeMenuData
     * @param array $data
     * @throws \Exception
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private function mergeContentMenus(array $mergeMenuData,array $data)
    {
        # 模板文件验证
        $contentMenuPath = $this->pluginTplPath . "menus/content/index.json";
        if (!file_exists($contentMenuPath)) {
            throw new Exception("内容系统菜单文件不存在");
        }
        $articleMenuPath = $this->pluginTplPath . "menus/content/articles.json";
        if (!file_exists($articleMenuPath)) {
            throw new Exception("文章系统菜单文件不存在");
        }
        $tagMenuPath = $this->pluginTplPath . "menus/content/tags.json";
        if (!file_exists($tagMenuPath)) {
            throw new Exception("单页系统菜单文件不存在");
        }
        $adsMenuPath = $this->pluginTplPath . "menus/content/ads.json";
        if (!file_exists($adsMenuPath)) {
            throw new Exception("广告系统菜单文件不存在");
        }
        
        # 内容系统
        if ($data['is_article'] == '20' || $data['is_page'] == '20' || $data['is_image'] == '20') {
            $menuData = file_get_contents($contentMenuPath);
            $menuData = json_decode($menuData, true);
            $menuData = PluginMgr::parseMenus($menuData);
            $mergeMenuData = array_merge($mergeMenuData,$menuData);
        }
        # 文章系统
        if ($data['is_article'] =='20') {
            $menuData = file_get_contents($articleMenuPath);
            $menuData = json_decode($menuData, true);
            $menuData = PluginMgr::parseMenus($menuData);
            $mergeMenuData = array_merge($mergeMenuData,$menuData);
        }
        # 单页系统
        if ($data['is_page'] == '20') {
            $menuData = file_get_contents($tagMenuPath);
            $menuData = json_decode($menuData, true);
            $menuData = PluginMgr::parseMenus($menuData);
            $mergeMenuData = array_merge($mergeMenuData,$menuData);
        }
        # 广告系统
        if ($data['is_image'] == '20') {
            $menuData = file_get_contents($adsMenuPath);
            $menuData = json_decode($menuData, true);
            $menuData = PluginMgr::parseMenus($menuData);
            $mergeMenuData = array_merge($mergeMenuData,$menuData);
        }
        return $mergeMenuData;
    }

    /**
     * 合并所有平台菜单
     * @param string $filePath
     * @param array $mergeMenuData
     * @param array $data
     * @throws \Exception
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private function mergePlatformMenus(array $mergeMenuData,array $data)
    {
        if (!in_array('mini_wechat',$data['platform']) && !in_array('douyin',$data['platform'])) {
            return $mergeMenuData;
        }
        # 模板文件验证
        $platformMenuPath = $this->pluginTplPath . "menus/platform/index.json";
        if (!file_exists($platformMenuPath)) {
            throw new Exception("平台系统菜单文件不存在");
        }
        # 内容系统
        $menuData = file_get_contents($platformMenuPath);
        $menuData = json_decode($menuData, true);
        $menuData = PluginMgr::parseMenus($menuData);
        $mergeMenuData = array_merge($mergeMenuData,$menuData);

        # 微信小程序
        if (in_array('mini_wechat',$data['platform'])) {
            # 验证菜单
            $weixinAppletMenuPath = $this->pluginTplPath . "menus/platform/weixin_applet.json";
            if (!file_exists($weixinAppletMenuPath)) {
                throw new Exception("平台-微信小程序菜单文件不存在");
            }
            $menuData = file_get_contents($weixinAppletMenuPath);
            $menuData = json_decode($menuData, true);
            $menuData = PluginMgr::parseMenus($menuData);
            $mergeMenuData = array_merge($mergeMenuData,$menuData);
        }
        
        # 微信流量主
        if (in_array('weixin_ad',$data['platform']) && $data['is_ad'] === '20') {
            # 验证菜单
            $weixinAdMenuPath = $this->pluginTplPath . "menus/platform/weixin_ad.json";
            if (!file_exists($weixinAdMenuPath)) {
                throw new Exception("平台-微信流量主菜单文件不存在");
            }
            $menuData = file_get_contents($weixinAdMenuPath);
            $menuData = json_decode($menuData, true);
            $menuData = PluginMgr::parseMenus($menuData);
            $mergeMenuData = array_merge($mergeMenuData,$menuData);
        }
        # 抖音流量主
        if (in_array('douyin_ad',$data['platform']) && $data['is_ad'] === '20') {
            # 验证菜单
            $weixinAdMenuPath = $this->pluginTplPath . "menus/platform/weixin_ad.json";
            if (!file_exists($weixinAdMenuPath)) {
                throw new Exception("平台-微信流量主菜单文件不存在");
            }
            $menuData = file_get_contents($weixinAdMenuPath);
            $menuData = json_decode($menuData, true);
            $menuData = PluginMgr::parseMenus($menuData);
            $mergeMenuData = array_merge($mergeMenuData,$menuData);
        }

        # 返回所有菜单
        return $mergeMenuData;
    }

    
    /**
     * 写入菜单
     * @param string $filePath
     * @param array $data
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private function writeMenus(string $filePath,array $data)
    {
        $menus    = PluginMgr::getMenuList($this->teamPluginName);
        $menus    = array_merge($menus, $data);
        $menus    = Data::channelLevel($menus, 0, '', 'id', 'pid');
        $menus    = $this->checkData($menus);
        $menuList = json_encode($menus, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($filePath, $menuList);
    }

    /**
     * 递归处理数据
     * @param array $data
     * @return array
     * @author John
     */
    private function checkData(array $data)
    {
        $data = array_values($data);
        foreach ($data as $key => $value) {
            if (isset($value['_level'])) {
                unset($data[$key]['_level']);
            }
            if (isset($value['_html'])) {
                unset($data[$key]['_html']);
            }
            if (!empty($value['children'])) {
                $data[$key]['children'] = $this->checkData($value['children']);
            }
        }
        return $data;
    }
}