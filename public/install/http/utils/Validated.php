<?php
use YcOpen\CloudService\Cloud;
use YcOpen\CloudService\Request\LoginRequest;

class Validated
{
    /**
     * 检测根目录是否有写入权限
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function rootPathAuth()
    {
        $path = ROOT_PATH . '/.env';
        if (is_writable($path)) {
            throw new Exception('请给予' . ROOT_PATH . '写入权限');
        }
    }

    /**
     * 验证数据库
     * @param array $data
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function validateDatabase(array $data)
    {
        if (empty($data['host'])) {
            throw new PDOException('请输入主机地址');
        }
        if (empty($data['database'])) {
            throw new PDOException('请输入数据库名称');
        }
        if (empty($data['username'])) {
            throw new PDOException('请输入数据库用户');
        }
        if (empty($data['password'])) {
            throw new PDOException('请输入数据库密码');
        }
        if (empty($data['port'])) {
            throw new PDOException('请输入数据库端口');
        }
        $dsn    = "mysql:host={$data['host']};dbname={$data['database']};port={$data['port']};";
        $params = [
            \PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8mb4",
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_TIMEOUT => 5,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ];
        new \PDO($dsn, $data['username'], $data['password'], $params);
    }
    
    /**
     * 云服务登录
     * @param array $data
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function validateCloud(array $data)
    {
        $req = new LoginRequest;
        $req->login();
        $req->username = $data['username'];
        $req->password = $data['password'];
        $req->scode = 'no';
        $cloud = new Cloud($req);
        $cloud->send();
    }

    /**
     * 验证站点
     * @param array $data
     * @throws \Exception
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function validateSite(array $data)
    {
        if (!isset($data['web_name']) || empty($data['web_name'])) {
            throw new Exception('请输入站点名称');
        }
        if (!isset($data['web_url']) || empty($data['web_url'])) {
            throw new Exception('请输入站点域名');
        }
        if (!filter_var($data['web_url'], FILTER_SANITIZE_URL)) {
            throw new Exception('请输入正确的域名地址');
        }
        if (!isset($data['username']) || empty($data['username'])) {
            throw new Exception('请输入站点管理员账号');
        }
        if (strlen($data['username']) < 5) {
            throw new Exception('管理员账号长度不能小于5位');
        }
        if (!isset($data['password']) || empty($data['password'])) {
            throw new Exception('请输入站点管理员密码');
        }
        if (strlen($data['password']) < 6 || strlen($data['password']) > 20) {
            throw new Exception('管理员密码不能小于6位或大于20位');
        }
    }
}