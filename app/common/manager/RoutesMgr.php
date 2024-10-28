<?php

namespace app\common\manager;

use app\common\manager\PluginMgr;
use Exception;
use app\common\model\SystemAuthRule;
use Webman\Route;
use support\Request;

/**
 * 路由管理器
 * 仅用于后端路由管理策略
 * @author 贵州猿创科技有限公司
 * @copyright (c) 贵州猿创科技有限公司
 */
class RoutesMgr
{
    // gzip开启对应文件返回类型
    private static $mimeContentType = [
        'js' => 'application/javascript',
        'json' => 'application/json',
        'css' => 'text/css',
    ];

    /**
     * 初始化路由
     *
     * @return void
     */
    public static function init()
    {
        // 注册已安装路由
        if (file_exists(base_path('/.env'))) {
            // 注册基础路由
            self::installed();
            // 映射模块名
            $modules = config('admin', []);
            // 注册后台视图路由
            self::installAdminView($modules);
            // 注册应用插件后台视图
            self::installPluginAdminView();
        }
    }

    /**
     * 注册已安装插件后台视图
     * @return void
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-05
     */
    private static function installPluginAdminView()
    {
        $plugins = PluginMgr::getLocalPlugins();
        if (!$plugins) {
            return;
        }
        foreach ($plugins as $plugin_name => $version) {
            $configPath = base_path("/plugin/{$plugin_name}/config/admin.php");
            if (file_exists($configPath)) {
                $data = require $configPath;
                foreach ($data as $key => $value) {
                    $adminName  = $value ? "/{$value}" : '';
                    $data[$key] = "app/{$plugin_name}{$adminName}";
                }
                // 注册插件后台视图
                self::installAdminView($data);
            }
        }
    }

    /**
     * 注册已安装后台视图
     * @return void
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-05
     */
    private static function installAdminView(array $modules)
    {
        if (!$modules) {
            return;
        }
        // 视图路径
        $viewPath = str_replace('\\', '/', base_path('/view'));
        // 批量注册静态模块路由
        foreach ($modules as $moduleAlias) {
            Route::group("/{$moduleAlias}", function () use ($viewPath, $moduleAlias) {
                // 注册不带结尾斜杠跳转
                Route::get('', function () use ($moduleAlias) {
                    return redirect("/{$moduleAlias}/");
                });
                // 注册实际后台访问静态页面
                Route::get("/", function (Request $request, $path = '') use ($viewPath) {
                    if (strpos($path, '..') !== false) {
                        return response('<h1>400 Bad Request</h1>', 400);
                    }
                    $file = "{$viewPath}/index.html";
                    if (!is_file($file)) {
                        return response('<h1>404 Not Found</h1>', 404);
                    }
                    return response()->withFile($file);
                });
                // 注册静态资源
                Route::get(
                    "/assets/[{path:.+}]",
                    function (Request $request, $path = '') use ($viewPath) {
                        if (strpos($path, '..') !== false) {
                            return response('<h1>400 Bad Request</h1>', 400);
                        }
                        $file = "{$viewPath}/assets/{$path}";
                        if (!is_file($file)) {
                            return response('<h1>404 Not Found</h1>', 404);
                        }
                        $response = response();
                        // 开启GZIP压缩
                        $gzipFile = "{$file}.gz";
                        if (file_exists($gzipFile)) {
                            $extension         = pathinfo($file, PATHINFO_EXTENSION);
                            $mime_content_type = 'text/plain';
                            if (isset(self::$mimeContentType[$extension])) {
                                $mime_content_type = self::$mimeContentType[$extension];
                            }
                            $file = $gzipFile;
                            $response->withHeaders([
                                'Content-Type' => $mime_content_type,
                                'Content-Encoding' => 'gzip'
                            ]);
                        }
                        return $response->withFile($file);
                    }
                );
            });
        }
    }

    /**
     * 注册已安装路由（接口）
     * @throws Exception
     * @return void
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-12
     */
    private static function installed()
    {
        $order  = [
            'sort' => 'asc',
            'id' => 'asc'
        ];
        $routes = SystemAuthRule::where('is_api', '1')
            ->order($order)
            ->select()
            ->toArray();

        // 是否启用控制器后缀
        $_suffix = config('app.controller_suffix', '');
        // 动态注册所有路由
        Route::group('/', function () use ($routes, $_suffix) {
            foreach ($routes as $value) {
                if (strpos($value['path'], '/') === false) {
                    echo "路由注册失败,path不符合规则,路由{$value['path']}";
                    continue;
                }
                list($controller, $action) = explode('/', $value['path']);
                $controller                = ucfirst($controller);
                $moduleName                = getModule($value['module']);
                $path                      = "{$moduleName}/{$controller}/{$action}";
                $namespace                 = "{$value['namespace']}{$controller}{$_suffix}@{$action}";
                Route::add($value['method'], $path, $namespace);
            }
        });
    }

    /**
     * 获取应用菜单
     * @param string $pluginName
     * @throws \Exception
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getPluginRoutes(string $pluginName)
    {
        $pluginMenuJsonPath = base_path("/plugin/{$pluginName}/menu.json");
        if (!file_exists($pluginMenuJsonPath)) {
            throw new Exception("插件JSON菜单文件不存在,请检查插件是否安装成功");
        }
        $menus = json_decode(file_get_contents($pluginMenuJsonPath), true);
        $menus = self::getPluginMenuList($menus);
        $data = [
            'active' => 'Index/index',
            'list' => empty($menus) ? [] : $menus
        ];
        return $data;
    }

    /**
     * 将多层级菜单转为二维数组菜单
     * @param array $data
     * @param array $list
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private static function getPluginMenuList(array $data, array $list = [], int &$id = 1, int $pid = 0)
    {
        foreach ($data as $value) {
            $item        = $value;
            $item['pid'] = $pid;
            $item['id']  = $id;
            $item['method'] = is_array($value['method']) ? current($value['method']) : 'GET';
            unset($item['children']);
            $list[] = $item;
            $id++;
            if (!empty($value['children'])) {
                $list = array_merge(self::getPluginMenuList($value['children'], $list, $id, $item['id']));
            }
        }
        return $list;
    }
}