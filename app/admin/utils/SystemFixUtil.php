<?php
namespace app\admin\utils;
use app\common\service\SystemInfoService;
use Exception;
use think\facade\Db;
use zjkal\MysqlHelper;

class SystemFixUtil
{
    /**
     * 备份数据库
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function backup()
    {
        $backUpPath = root_path().'/backup';
        if (!is_writable($backUpPath)) {
            throw new Exception('备份目录没有写入权限');
        }
        # 获取系统信息
        $systemInfo   = SystemInfoService::version();
        $dateTime = date('Y年m月d日H时i分');
        # 文件名称
        $backFileName = "{$dateTime}_{$systemInfo['version_name']}-{$systemInfo['version']}.sql";
        # 数据库信息
        $host = config('database.connections.mysql.hostname', '');
        $database = config('database.connections.mysql.database', '');
        $user = config('database.connections.mysql.username', '');
        $password = config('database.connections.mysql.password', '');
        $charset = config('database.connections.mysql.charset', '');
        $port = config('database.connections.mysql.hostport', '');
        $prefix = config('database.connections.mysql.prefix', '');
        # 链接数据库
        $mysql = new MysqlHelper($user, $password, $database, $host, $port, $prefix, $charset);
        # 开始导出数据
        $mysql->exportSqlFile("{$backUpPath}/{$backFileName}");
    }

    /**
     * 修复菜单数据
     * @param string $component
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function fixMenus()
    {
        $menuSqlPath = public_path('/install/http/data/sql').'yc_system_auth_rule.sql';
        if (!file_exists($menuSqlPath)) {
            throw new Exception('菜单数据文件不存在');
        }
        # 数据库信息
        $host = config('database.connections.mysql.hostname', '');
        $database = config('database.connections.mysql.database', '');
        $user = config('database.connections.mysql.username', '');
        $password = config('database.connections.mysql.password', '');
        $charset = config('database.connections.mysql.charset', '');
        $port = config('database.connections.mysql.hostport', '');
        $prefix = config('database.connections.mysql.prefix', '');
        # 链接数据库
        $mysql = new MysqlHelper($user, $password, $database, $host, $port, $prefix, $charset);
        $mysql->importSqlFile($menuSqlPath);
    }
    
    /**
     * 获取表名称与字段
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getTableFields()
    {
        $files = glob(public_path().'install/http/data/sql/*.sql');
        $data  = [];
        foreach ($files as $key => $path) {
            # 文件名称
            $fileName = basename($path,'.sql');
            # 表名称
            $tableName = str_replace('yc_','',$fileName);
            # 获取字段名称
            $fields = self::getFieldsType($path);
            # 组装数据
            $data[$key] = [
                'name'          => $tableName,
                'fields'        => $fields
            ];
        }
        return $data;
    }

    /**
     * 获取字段名称+字段类型
     * @param string $path
     * @throws \Exception
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private static function getFieldsType(string $path)
    {
        if (!file_exists($path)) {
            throw new Exception('SQL文件不存在');
        }
        $sqlContent = file_get_contents($path);
        $sqlContent = explode("\n", $sqlContent);
        $data    = [];
        foreach ($sqlContent as $value) {
            # 判断内容是否存在,
            if (strpos($value, ',') !== false) {
                # 移除多余字符串
                $newValue = trim($value," \n\r\t\v\x00,");
                # 匹配字段名称+类型
                preg_match("/`(.*?)`\s+(.*?) /", $newValue, $matches);
                # 字段名称
                $fiedName = $matches[1] ?? '';
                # 字段类型+长度
                $fieldType = $matches[2] ?? '';
                # 检测是否存在表前缀
                if (strrpos($fiedName,'__PREFIX__') !== false) {
                    continue;            
                }
                # 检测是否存在类型长度
                if (strpos($fieldType,'(') !== false) {
                    preg_match("/(.*?)\(/", $fieldType, $matches);
                    $fieldType = $matches[1] ?? $fieldType;
                }
                # 禁止操作主键
                if ($fiedName === 'id') {
                    continue;
                }
                $data[$fiedName] = $fieldType;    
            }
        }
        # 移除空数组
        $data = array_filter($data);
        # 返回数据
        return $data;
    }

    /**
     * 获取文件字段名称
     * @param string $path
     * @throws \Exception
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private static function getFields(string $path)
    {
        if (!file_exists($path)) {
            throw new Exception('SQL文件不存在');
        }
        $sqlContent = file_get_contents($path);
        $sqlContent = explode("\n", $sqlContent);
        $data    = [];
        foreach ($sqlContent as $value) {
            # 判断内容是否存在,
            if (strpos($value, ',') !== false) {
                # 移除多余字符串
                $newValue = trim($value," \n\r\t\v\x00,");
                # 匹配字符串中``之间内容
                preg_match_all('/`(.*)` /', $newValue, $matches);
                $fieldName = $matches[1][0] ?? '';
                if (strpos($fieldName,'__PREFIX__') === false) {
                    # 禁止操作主键
                    if ($fieldName === 'id') {
                        continue;
                    }
                    $data[$fieldName] = "{$newValue};";
                }
            }
        }
        return $data;
    }

    /**
     * 查找所需更新的SQL语句
     * @param string $tableName
     * @param array $fieldData
     * @throws \Exception
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getSql(string $tableName, array $fieldData)
    {
        # 表前缀
        $prefix = config('database.connections.mysql.prefix', '');
        $fileName = str_replace($prefix, 'yc_', $tableName);
        # SQL文件路径
        $sqlFile = public_path('install/http/data/sql').$fileName.'.sql';
        # 获取字段+SQL列表
        $sqlData    = self::getFields($sqlFile);
        # 查询表所有字段
        $fields = Db::query("SHOW FULL COLUMNS FROM {$tableName}");
        $fieldList = array_column($fields, null,'Field');
        $data = [];
        foreach ($fieldData as $field) {
            # 禁止操作主键
            if ($field === 'id') {
                continue;
            }
            $sql = $sqlData[$field] ?? '';
            # 检测字段是否存在
            if (isset($fieldList[$field])) {
                $dataValue = 'null';
                if (in_array($field, ['create_time','update_time'])) {
                    $dataValue = date('Y-m-d H:i:s');
                }
                # 清空数据
                $update = "UPDATE `{$tableName}` SET `{$field}` = '{$dataValue}' WHERE `{$field}` <> 'null'";
                # 修改字段
                $data[$field] = "{$update};\nALTER TABLE `{$tableName}` MODIFY {$sql}";
            } else {
                # 新增字段
                $data[$field] = "ALTER TABLE `{$tableName}` ADD {$sql}";
            }
        }
        return $data;
    }
}