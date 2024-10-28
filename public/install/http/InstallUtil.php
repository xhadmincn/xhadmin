<?php

use app\common\utils\Password;
use think\facade\Config;
use think\facade\Db;
use YcOpen\CloudService\Cloud;
use YcOpen\CloudService\Request\SystemUpdateRequest;
use zjkal\MysqlHelper;

/**
 * 数据操作部分
 * @author 贵州猿创科技有限公司
 * @copyright (c) 2023
 */
class InstallUtil
{
    /**
     * 安装表结构
     * @param array $data
     * @return string
     * @author John
     */
    public static function structure(array $data)
    {
        # 数据库连接
        $total    = isset($_GET['total']) ? intval($_GET['total']) : 0;
        $database = isset($data['database']) ? $data['database'] : [];
        if (empty($database)) {
            return Json::fail('获取安装数据库配置失败');
        }
        # 获取SQL文件树
        $sqlFiles = glob(XH_INSTALL_HTTP_PATH . '/data/sql/*.sql');
        # 检测全部数据表是否安装完成
        if ($total >= count($sqlFiles)) {
            return Json::json('安装数据库结构完成...', 200, [
                'next' => 'database'
            ]);
        }
        # 获取SQL文件
        $sqlFile = isset($sqlFiles[$total]) ? $sqlFiles[$total] : '';
        if (!$sqlFile) {
            throw new Exception('获取SQL文件失败');
        }
        # 实例操作类
        $mysql = new MysqlHelper(
            $database['username'],
            $database['password'],
            $database['database'],
            $database['host'],
            $database['port'],
            $database['prefix'],
            $database['charset']
        );
        # 导入SQL文件
        $mysql->importSqlFile($sqlFile, $database['prefix']);
        # 获取数据表名称
        $installName = str_replace(['php_', 'yc_',], '', basename($sqlFile, '.sql'));
        # 返回成功
        return Json::json("【{$installName}】 安装数据表成功...", 200, [
            'next' => 'structure',
            'total' => $total + 1
        ]);
    }

    /**
     * 安装表数据
     * @param array $data
     * @return string
     * @author John
     */
    public static function database(array $data)
    {
        $database = isset($data['database']) ? $data['database'] : null;
        $site     = isset($data['site']) ? $data['site'] : null;
        if (!$site || !$database) {
            return Json::fail('安装数据失败');
        }
        $config = [
            'connections' => [
                'mysql' => [
                    // 数据库类型
                    'type' => $database['type'],
                    // 服务器地址
                    'hostname' => $database['host'],
                    // 数据库名
                    'database' => $database['database'],
                    // 用户名
                    'username' => $database['username'],
                    // 密码
                    'password' => $database['password'],
                    // 端口
                    'hostport' => $database['port'],
                    // 数据库编码默认采用utf8
                    'charset' => $database['charset'],
                    // 数据库表前缀
                    'prefix' => $database['prefix'],
                ]
            ]
        ];
        Config::set($config, 'database');
        # 写入站点名称
        $configData = [
            'create_at' => date('Y-m-d H:i:s'),
            'update_at' => date('Y-m-d H:i:s'),
            'group' => 'system',
            'value' => json_encode([
                'web_name'      => $site['web_name'],
                'web_url'       => $site['web_url'],
                'admin_logo'    => '/image/logo.png',
            ], JSON_UNESCAPED_UNICODE),
        ];
        Db::name('system_config')->save($configData);
        # 附件库配置
        $configData = [
            'create_at'         => date('Y-m-d H:i:s'),
            'update_at'         => date('Y-m-d H:i:s'),
            'group'             => 'upload',
            'value'             => json_encode([
                'upload_drive'  => 'local',
                'children'      => [
                    'local'     => [
                        'url'   => $site['web_url'],
                        'root'  => 'uploads'
                    ]
                ],
            ], JSON_UNESCAPED_UNICODE),
        ];
        Db::name('system_config')->save($configData);

        # 获取管理员参数
        $username = isset($site['username']) ? $site['username'] : '';
        $password = isset($site['password']) ? $site['password'] : '';
        # 写入管理员信息
        $password = Password::passwordHash($password);
        $data     = [
            'create_at'     => date('Y-m-d H:i:s'),
            'update_at'     => date('Y-m-d H:i:s'),
            'username'      => $username,
            'password'      => $password,
            'role_id'       => 1,
            'nickname'      => '系统管理员',
            'status'        => '20',
            'is_system'     => '20'
        ];
        Db::name('system_admin')->save($data);

        # 安装完成
        return Json::json('安装站点数据完成...', 200, [
            'next' => 'config'
        ]);
    }

    /**
     * 安装配置项文件
     * @param array $data
     * @return string
     * @author John
     */
    public static function config(array $data)
    {
        # 安装Env配置文件
        self::installEnv($data);
        # 安装完成
        return Json::success('安装完成，即将跳转...');
    }

    /**
     * 安装配置文件
     * @param array $data
     * @throws Exception
     * @return bool|int
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-18
     */
    private static function installEnv(array $data): int|false
    {
        # 数据库获取参数
        $database = isset($data['database']) ? $data['database'] : null;
        if (!$database) {
            throw new Exception('获取安装数据失败');
        }
        # 数据库参数
        $type           = isset($database['type']) ? $database['type'] : '';
        $host           = isset($database['host']) ? $database['host'] : '';
        $dbName         = isset($database['database']) ? trim($database['database']) : '';
        $port           = isset($database['port']) ? $database['port'] : '';
        $user           = isset($database['username']) ? trim($database['username']) : '';
        $password       = isset($database['password']) ? trim($database['password']) : '';
        $prefix         = isset($database['prefix']) ? trim($database['prefix']) : '';
        # redis获取参数
        $redisForm      = isset($data['redis']) ? $data['redis'] : [];
        $redisHost      = isset($redisForm['host']) ? $redisForm['host'] : 'localhost';
        $redisPort      = isset($redisForm['port']) ? $redisForm['port'] : '6379';
        $redisPass      = isset($redisForm['password']) ? $redisForm['password'] : '';
        $redisPrefix    = isset($redisForm['prefix']) ? $redisForm['prefix'] : '';

        # 拼接配置文件路径
        $envTplPath = XH_INSTALL_HTTP_PATH . "/data/env.tpl";
        $envPath    = ROOT_PATH . "/.env";

        # 读取配置文件
        $envConfig = file_get_contents($envTplPath);

        # 替换配置文件参数
        $str1      = [
            "{TYPE}",
            "{HOSTNAME}",
            "{DATABASE}",
            "{USERNAME}",
            "{PASSWORD}",
            "{HOSTPORT}",
            "{PREFIX}",
            "{REDIS_HOST}",
            "{REDIS_PORT}",
            "{REDIS_PASSWORD}",
            "{REDIS_PREFIX}"
        ];
        $str2      = [
            $type,
            $host,
            $dbName,
            $user,
            $password,
            $port,
            $prefix,
            $redisHost,
            $redisPort,
            $redisPass,
            $redisPrefix
        ];
        $envConfig = str_replace($str1, $str2, $envConfig);

        # 写入配置文件
        return file_put_contents($envPath, $envConfig);
    }
}
