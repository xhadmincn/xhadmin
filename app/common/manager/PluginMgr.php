<?php

namespace app\common\manager;

use app\common\service\SystemInfoService;
use Exception;
use YcOpen\CloudService\Cloud;
use YcOpen\CloudService\Request;
use YcOpen\CloudService\Request\UserRequest;

class PluginMgr
{

    /**
     * 检测是否开发者
     * @return bool
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function isDeveloper()
    {
        $req = new UserRequest;
        $req->info();
        $cloud = new Cloud($req);
        $data  = $cloud->send()->toArray();
        $isDeveloper = $data['is_dev'] == 1 ? true : false;
        return $isDeveloper;
    }

    /**
     * 检测应用对SAAS版本支持
     * @param string $name
     * @return bool
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */

    public static function checkPluginSaasVersion(string $name)
    {
        # 检测是否开发者
        if (self::isDeveloper()) {
            return true;
        }
        # 获取应用信息
        $systemInfo = SystemInfoService::info();
        # 获取本地应用版本
        $installedVersion = self::getPluginVersion($name);
        # 获取云端应用详情
        $data = Request::Plugin()->detail([
            'name' => $name,
            'version' => $installedVersion,
            'saas_version' => $systemInfo['system_version'],
            'local_version' => $installedVersion,
        ])->response()->toArray();
        if (empty($data['local_version'])) {
            throw new Exception('云端应用数据错误');
        }
        return $systemInfo['system_version'] >= $data['local_version']['saas_version'];
    }

    /**
     * 获取本地插件版本
     * @param mixed $name
     * @return mixed
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-08
     */
    public static function getPluginVersion($name, $versionName = 'version')
    {
        if (!is_dir(root_path() . "/plugin/{$name}")) {
            return 1;
        }
        $json = root_path() . "/plugin/{$name}/version.json";
        if (!is_file($json)) {
            return 1;
        }
        $config = json_decode(file_get_contents($json), true);
        return isset($config[$versionName]) ? $config[$versionName] : 1;
    }

    /**
     * 获取应用版本数据
     * @param mixed $name
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getPluginVersionData($name)
    {
        $version = [
            'version' => 1,
            'version_name' => '1.0.0'
        ];
        if (!is_dir(root_path() . "/plugin/{$name}")) {
            return $version;
        }
        $json = root_path() . "/plugin/{$name}/version.json";
        if (!is_file($json)) {
            return $version;
        }
        $config = json_decode(file_get_contents($json), true);
        # 返回数据
        return $config ? $config : $version;
    }

    /**
     * 获取已安装的插件列表
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-08
     */
    public static function getLocalPlugins(): array
    {
        if (!is_dir(root_path() . '/plugin/')) {
            return [];
        }
        clearstatcache();
        $installed    = [];
        $plugin_names = array_diff(scandir(root_path() . '/plugin/'), array('.', '..')) ?: [];
        foreach ($plugin_names as $plugin_name) {
            if (is_dir(root_path() . "/plugin/{$plugin_name}") && $version = self::getPluginVersion($plugin_name)) {
                $installed[$plugin_name] = $version;
            }
        }
        return $installed;
    }

    /**
     * 获取原始菜单数据
     * @param string $name
     * @return mixed
     * @author John
     */
    public static function getOriginMenus(string $name, string $file = 'menus.json')
    {
        $menuJsonPath = root_path() . "/plugin/{$name}/{$file}";
        if (!file_exists($menuJsonPath)) {
            throw new Exception('插件菜单文件不存在');
        }
        $menusContent = file_get_contents($menuJsonPath);
        if (empty($menusContent)) {
            throw new Exception('插件菜单文件内容为空');
        }
        $menus = json_decode($menusContent, true);
        if (is_null($menus)) {
            throw new Exception('插件菜单文件内容格式错误');
        }
        return $menus;
    }

    /**
     * 获取插件菜单
     * @param string $name
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getMenus(string $name)
    {
        # 获取原始菜单数据
        $menus = self::getOriginMenus($name);
        # 处理菜单数据
        $data = self::parseMenus($menus);
        # 处理可展示数据
        foreach ($data as $key => $value) {
            $value['show'] = $value['show'] == '20' ? '1' : '0';
            $value['is_api'] = $value['is_api'] == '20' ? '1' : '0';
            $value['is_system'] = $value['is_system'] == '20' ? '1' : '0';
            $value['component'] = $value['component'] == 'none/index' ? '' : $value['component'];
            if (is_array($value['method'])) {
                $value['method'] = current($value['method']);
            }
            $data[$key] = $value;
        }
        # 返回数据
        return $data;
    }

    /**
     * 获取可展示菜单数据
     * @param string $name
     * @return array
     * @author John
     */
    public static function getMenuList(string $name)
    {
        # 获取原始菜单数据
        $menus = self::getOriginMenus($name);
        # 处理菜单数据
        $data = self::parseMenus($menus);
        # 处理可展示数据
        foreach ($data as $key => $value) {
            $data[$key] = $value;
        }
        # 返回数据
        return $data;
    }

    /**
     * 解析菜单数据
     * @param array $menus
     * @param array $data
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function parseMenus(array $menus, array $data = [])
    {
        foreach ($menus as $value) {
            # 设置临时处理数据
            $menuData = $value;
            # 设置图标
            $menuData['icon'] = isset($value['icon']) ? $value['icon'] : '';
            # 菜单组件
            $menuData['component'] = isset($value['component']) ? $value['component'] : 'none/index';
            # 设置默认附带参数
            $menuData['auth_params'] = isset($value['auth_params']) ? $value['auth_params'] : '';
            # 设置是否系统菜单
            $menuData['is_system'] = isset($value['is_system']) ? $value['is_system'] : '10';
            # 是否默认菜单
            $menuData['is_default'] = isset($value['is_default']) ? $value['is_default'] : '10';
            # 菜单是否显示
            $menuData['show'] = isset($value['show']) ? $value['show'] : '10';
            # 是否接口
            $menuData['is_api'] = isset($value['is_api']) ? $value['is_api'] : '10';
            # 删除子级菜单
            if (isset($menuData['children'])) {
                unset($menuData['children']);
            }
            # 设置菜单数据
            $data[] = $menuData;
            # 递归处理子级菜单
            if (isset($value['children']) && !empty($value['children'])) {
                $data = array_merge($data, self::parseMenus($value['children']));
            }
        }
        return $data;
    }
}
