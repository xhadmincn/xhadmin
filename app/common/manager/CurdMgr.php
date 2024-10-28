<?php
namespace app\common\manager;

use app\common\manager\DbMgr;
use app\common\service\PluginService;
use Exception;
use think\helper\Str;

class CurdMgr extends CurdRule
{
    /**
     * 获取菜单生成预览数据
     * @param string $menuPath
     * @param string $database
     * @param string $prefixTableName
     * @param string $app_name
     * @param string $tableName
     * @param mixed $pidPath
     * @param string $menu_name
     * @throws \Exception
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getMenusPreView(
        string $menuPath,
        string $database,
        string $prefixTableName,
        string $app_name,
        string $tableName,
        mixed $pidPath,
        string $menu_name
    ) {
        if (!file_exists($menuPath)) {
            throw new Exception('应用菜单文件不存在');
        }
        # 获取菜单数据
        $menus = json_decode(file_get_contents($menuPath), true);
        $menus = empty($menus) ? [] : $menus;
        # 表名转类名
        $className = Str::studly($tableName);
        # 新菜单数据
        $menuData = [
            'module' => "app/{$app_name}/admin",
            'path' => "{$className}/index",
            'title' => "{$menu_name}管理",
            'method' => ['GET'],
            'component' => 'table/index',
            'auth_params' => '',
            'icon' => '',
            'show' => '1',
            'children' => [
                [
                    'module' => "app/{$app_name}/admin",
                    'path' => "{$className}/add",
                    'title' => "{$menu_name}-添加",
                    'method' => ['GET', 'POST'],
                    'component' => 'form/index',
                    'auth_params' => '',
                    'icon' => '',
                    'show' => '0',
                    'children' => []
                ],
                [
                    'module' => "app/{$app_name}/admin",
                    'path' => "{$className}/edit",
                    'title' => "{$menu_name}-修改",
                    'method' => ['GET', 'PUT'],
                    'component' => 'form/index',
                    'auth_params' => '',
                    'icon' => '',
                    'show' => '0',
                    'children' => []
                ],
                [
                    'module' => "app/{$app_name}/admin",
                    'path' => "{$className}/del",
                    'title' => "{$menu_name}-删除",
                    'method' => ['GET', 'DELETE'],
                    'component' => '',
                    'auth_params' => '',
                    'icon' => '',
                    'show' => '0',
                    'children' => []
                ],
                [
                    'module' => "app/{$app_name}/admin",
                    'path' => "{$className}/indexGetTable",
                    'title' => "{$menu_name}-表格规则",
                    'method' => ['GET'],
                    'component' => 'table/index',
                    'auth_params' => '',
                    'icon' => '',
                    'show' => '0',
                    'children' => []
                ]
            ]
        ];
        # 顶级菜单
        if (empty($pidPath)) {
            # 顶级菜单
            array_push($menus, [
                'module' => "app/{$app_name}/admin",
                'path' => "{$className}/group",
                'title' => $menu_name,
                'method' => ['GET'],
                'component' => '',
                'auth_params' => '',
                'icon' => 'HomeOutlined',
                'show' => '1',
                'children' => [
                    $menuData
                ]
            ]);
        } else {
            # 多层级菜单
            self::findPidIndex($menus, $pidPath, $menuData);
        }
        # 返回菜单数据
        return $menus;
    }

    /**
     * 递归查找父级菜单
     * @param mixed $menus
     * @param mixed $pidPath
     * @param mixed $menuData
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private static function findPidIndex(&$menus, $pidPath, $menuData)
    {
        $dd = $pidPath[0];
        unset($pidPath[0]);
        $pidPath = array_values($pidPath);
        if (empty($pidPath)) {
            $newData                = $menus[$dd]['children'];
            $menus[$dd]['children'] = array_merge($newData, $menuData);
        } else {
            self::findPidIndex($menus[$dd]['children'], $pidPath, $menuData);
        }
    }

    /**
     * 获取CURD代码
     * @param string $app_name
     * @param string $tableName
     * @throws \Exception
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getCurdCode(string $app_name, string $tableName)
    {
        # 检测是否选择应用
        if (!$app_name) {
            throw new Exception('请选择需要生成到的应用');
        }
        # 检测应用是否存在
        if (!is_dir(base_path("/plugin/{$app_name}"))) {
            throw new Exception('该应用不存在');
        }
        # 生成类名（转驼峰）
        $className = Str::studly($tableName);
        # 生成到指定路径
        $pluginAppPath = "/plugin/{$app_name}/app";
        $suffix        = config('app.controller_suffix', '');
        $suffix        = ucfirst($suffix);
        # 生成应用控制器
        $controllerPathRoot = "{$pluginAppPath}/admin/controller/{$className}{$suffix}.php";
        $controllerPath     = base_path($controllerPathRoot);
        if (!is_dir(dirname($controllerPath))) {
            throw new Exception('应用控制器目录错误');
        }
        # 获取表前缀
        $prefix = config('database.connections.mysql.prefix');
        # 获取表信息
        $prefixTableName = "{$prefix}{$tableName}";
        $tableInfo       = self::getTableInfo($prefixTableName);
        # 获取表格规则
        $tableRule = self::getTableRule($app_name, $tableName);
        # 获取表单规则
        $addFormRule  = self::getFormRule($tableName, 'add');
        $editFormRule = self::getFormRule($tableName, 'edit');
        # CURD模板路径
        $sourcePath = app_path('/admin/tpl/curd');
        # 控制器模板
        $controllerSource = file_get_contents("{$sourcePath}/Controller.tpl");
        $str1             = [
            "{TABLE_COMMENT}",
            "{PLUGIN_NAME}",
            "{CLASS_NAME}",
            "{SUFFIX}",
            "{TABLE_RULE}",
            "{ADD_FORM_RULE}",
            "{EDIT_FORM_RULE}",
            "{DEL_RULE}"
        ];
        $str2             = [
            $tableInfo['TABLE_COMMENT'],
            $app_name,
            $className,
            $suffix,
            $tableRule,
            $addFormRule,
            $editFormRule,
        ];
        $controllerSource = str_replace($str1, $str2, $controllerSource);
        # 生成应用模型
        $modelPathRoot = "{$pluginAppPath}/model/{$className}.php";
        $modelPath     = base_path("{$pluginAppPath}/model/{$className}.php");
        if (!is_dir(dirname($modelPath))) {
            throw new Exception('应用模型目录错误');
        }
        $modelSource = file_get_contents("{$sourcePath}/Model.tpl");
        $modelSource = str_replace($str1, $str2, $modelSource);
        # 生成应用验证器
        $validatePathRoot = "{$pluginAppPath}/validate/{$className}Validate.php";
        $validatePath     = base_path("{$pluginAppPath}/validate/{$className}Validate.php");
        if (!is_dir(dirname($validatePath))) {
            throw new Exception('应用验证器目录错误');
        }
        $validateSource = file_get_contents("{$sourcePath}/Validate.tpl");
        $validateSource = str_replace($str1, $str2, $validateSource);
        # 返回代码
        $data = [
            'controllerPath' => $controllerPathRoot,
            'modelPath' => $modelPathRoot,
            'validatePath' => $validatePathRoot,
            'controller' => $controllerSource,
            'model' => $modelSource,
            'validate' => $validateSource,
        ];
        return $data;
    }

    /**
     * 获取表信息
     * @param string $prefixTableName
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    private static function getTableInfo(string $prefixTableName)
    {
        $sql       = "SELECT table_comment FROM INFORMATION_SCHEMA.TABLES 
        where table_name = '{$prefixTableName}';";
        $columnObj = DbMgr::instance()->select($sql);
        if (empty($columnObj)) {
            throw new Exception('获取字段数据失败');
        }
        isset($columnObj[0]) && $columnData = (array) $columnObj[0];
        if (empty($columnData)) {
            throw new Exception('表信息数据错误');
        }
        if (empty($columnData['TABLE_COMMENT'])) {
            throw new Exception('表注释数据错误');
        }
        return $columnData;
    }

    /**
     * 递归处理菜单数据
     * @param array $data
     * @param string $className
     * @param int $level
     * @param int $id
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function parentLevelList(array $data, string $className, string|null $id = null)
    {
        $list = [];
        foreach ($data as $key => $value) {
            $expl = explode('/', $value['path']);
            if (empty($expl[0])) {
                throw new Exception('path路径格式出错');
            }
            $list[$key]['label'] = $value['title'];
            if ($id != null) {
                $val = $id . '-' . $key;
            } else {
                $val = (string) $key;
            }
            $list[$key]['value']    = $val;
            $list[$key]['disabled'] = false;
            # 禁用自身
            if ($expl[0] === $className) {
                $list[$key]['disabled'] = true;
            }
            # ID自增
            if (!empty($value['children'])) {
                $list[$key]['children'] = self::parentLevelList($value['children'], $className, $val);
            }
        }
        return $list;
    }

    /**
     * 获取本地应用选项列表
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function getPlugins()
    {
        $plugins = PluginService::getLocalPlugins();
        $plugins = array_keys($plugins);
        $data    = [];
        foreach ($plugins as $key => $value) {
            $data[$key] = [
                'label' => $value,
                'value' => $value
            ];
        }
        return $data;
    }
}