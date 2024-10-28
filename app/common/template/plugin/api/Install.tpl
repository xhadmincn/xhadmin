<?php

namespace plugin\{TEAM_PLUGIN_NAME}\api;

use app\common\manager\DbMgr;
use think\facade\Db;
use think\facade\Log;
use zjkal\MysqlHelper;

class Install
{
    /**
     * 安装应用
     * @param mixed $version
     * @return bool
     * @author 楚羽幽
     */
    public static function install($version): bool
    {
        $sqlPath = root_path('plugin/{TEAM_PLUGIN_NAME}/api/database') . 'install.sql';
        if(!file_exists($sqlPath)){
            throw new \Exception("SQL文件不存在");
        }
        # sql
        $sql = file_get_contents($sqlPath);
        if(empty($sql)){
            return true;
        }
        # 检测不是标准前缀
        if(strpos($haystack, '`php_') !== false || strpos($haystack, '`yc_') !== false){
            # 替换前缀重新写入
            $prefixs = ['`php_','`yc_'];
            $sqlContent = str_replace($prefixs,'`__PREFIX__');
            file_put_contents($sqlPath,$sqlContent);
        }

        # 开始进入数据安装
        $_config = config('database');
        if (empty($_config['default']) || empty($_config['connections'])) {
            throw new \Exception("数据库链接配置错误");
        }
        $_mysql = $_config['connections'][$_config['default']];
        if (empty($_mysql)) {
            throw new \Exception($_config['default'] . "链接配置错误");
        }
        # 实例操作类
        $mysql = new MysqlHelper(
            $_mysql['username'],
            $_mysql['password'],
            $_mysql['database'],
            $_mysql['hostname'],
            $_mysql['hostport'],
            $_mysql['prefix'],
            $_mysql['charset']
        );
        # 导入SQL文件
        @$mysql->importSqlFile($sqlPath, $_mysql['prefix']);
        # 返回执行成功
        return true;
    }

    /**
     * 卸载应用
     * @param mixed $version
     * @return bool
     * @author 楚羽幽
     */
    public static function uninstall($version): bool
    {
        $_config = config('database');
        if (empty($_config['default']) || empty($_config['connections'])) {
            throw new \Exception("数据库链接配置错误");
        }
        $_mysql = $_config['connections'][$_config['default']];
        if (empty($_mysql)) {
            throw new \Exception($_config['default'] . "链接配置错误");
        }
        try {
            $dbconnect = new \PDO("mysql:host={$_mysql['hostname']}:{$_mysql['hostport']};dbname={$_mysql['database']}", $_mysql['username'], $_mysql['password']);
        } catch (\PDOException $error) {
            throw new \Exception('MySql:' . $error->getMessage());
        }
        $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME LIKE '" . $_mysql['prefix'] . "{PLUGIN_COMPLETE_NAME}_%';";
        $result = $dbconnect->query($sql);
        if ($result === false) {
            throw new \Exception('查询数据库失败');
        }
        $TABLE_NAME = [];
        if (is_object($result)) {
            $data = $result->fetchAll(\PDO::FETCH_ASSOC);
            if (empty($data)) {
                return true;
            }
            foreach ($data as $k => $val) {
                $TABLE_NAME[] = $val['TABLE_NAME'];
            }
        } else {
            throw new \Exception($result);
        }
        if (empty($TABLE_NAME)) {
            return true;
        }
        $result = $dbconnect->query("DROP TABLE " . implode(',', $TABLE_NAME));
        if ($result === false) {
            throw new \Exception('清除应用插件数据库失败');
        }
        return true;
    }

    /**
     * 更新应用
     * @param mixed $from_version
     * @param mixed $to_version
     * @param mixed $data
     * @return void
     * @author 楚羽幽
     */
    public static function update($from_version, $to_version, $data = null)
    {
        $prefix     = config('database.connections.mysql.prefix');
        $prefixs    = ['`php_', '`yc_'];
        try {
            # 连接原生PDO
            $connect = Db::connect();
            $connect->execute('show tables');
            $pdo = $connect->getPdo();
            foreach ($data as $value) {
                if (empty($value['file'])) {
                    continue;
                }
                if (empty($value['sql'])) {
                    continue;
                }
                try {
                    # 替换前缀
                    $sql = str_replace($prefixs, "`{$prefix}", $value['sql']);
                    Log::info("执行{TEAM_PLUGIN_NAME} - 更新SQL：{$sql}");
                    # 执行SQL
                    $pdo->exec($sql);
                    # 执行成功后删除文件
                    $filePath = root_path("plugin/{TEAM_PLUGIN_NAME}/api/database/update") . $value['file'];
                    file_exists($filePath) && unlink($filePath);
                } catch (\Throwable $th) {
                    Log::error("执行{TEAM_PLUGIN_NAME} - 更新SQL错误：{$th->getMessage()}");
                }
            }
        } catch (\Throwable $e) {
            Log::error("执行SAAS系统 - 更新SQL错误：{$e->getMessage()}");
        }
    }

    /**
     * 更新前
     * @param mixed $from_version
     * @param mixed $to_version
     * @return array
     * @author 楚羽幽
     */
    public static function beforeUpdate($from_version, $to_version)
    {
        # sql目录
        $sqlDir = root_path('plugin/{TEAM_PLUGIN_NAME}/api/database/update');
        if (!is_dir($sqlDir)) {
            return [];
        }
        # 获取sql文件
        $sqlFiles = array_values(array_diff(scandir($sqlDir), ['.', '..']));
        $extension = '.sql';
        $data      = [];
        foreach ($sqlFiles as $file) {
            if (!strpos($file, $extension)) {
                continue;
            }
            $sqlContent = file_get_contents("{$sqlDir}/{$file}");
            if (empty($sqlContent) && file_exists("{$sqlDir}/{$file}")) {
                unlink("{$sqlDir}/{$file}");
                continue;
            }
            $data[] = [
                'file' => $file,
                'sql'  => $sqlContent,
            ];
        }
        return $data;
    }
}
