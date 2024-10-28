<?php
/**
 * 安装逻辑
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class InstallController
{
    /**
     * 构造函数
     * @throws \Exception
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function __construct()
    {
        // 已安装
        $act = isset($_GET['a']) ? $_GET['a'] : '';
        if (file_exists(ROOT_PATH . '/.env') && $act !== 'complete') {
            throw new Exception('已安装', 200);
        }
    }

    /**
     * 获取安装协议
     * @return string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function index()
    {
        $content = file_get_contents(XH_INSTALL_HTTP_PATH . '/data/agreement.txt');
        $data    = [
            'text' => $content
        ];
        return Json::successRes($data);
    }

    /**
     * 环境检测
     * @return string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function environment()
    {
        require XH_INSTALL_HTTP_PATH . "/utils/Environment.php";
        $data = [
            'fun' => Environment::getVerifyFun(),
            'extra' => Environment::getVerifyExtra(),
            'dirData' => Environment::getDirData()
        ];
        return Json::successRes($data);
    }

    /**
     * 安装前验证
     * @return string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function database()
    {
        # 获取数据
        $post = $_POST;
        # 数据验证
        if (!isset($post['database'])) {
            return Json::fail('缺少数据库信息');        
        }
        if (!isset($post['site'])) {
            return Json::fail('缺少站点信息');        
        }

        # 数据处理
        $database = isset($post['database']) ? $post['database'] : [];
        $site     = isset($post['site']) ? $post['site'] : [];
        try {
            # 权限验证
            Validated::rootPathAuth();
            # 验证数据库
            Validated::validateDatabase($database);
            # 验证站点数据
            Validated::validateSite($site);
            # 返回数据
            return Json::successRes($post);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 安装数据
     * @return string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function install()
    {
        # 获取数据
        $step = isset($_GET['step']) ? $_GET['step'] : '';
        $post = $_POST;
        if ($step === 'structure') {
            # 安装表结构
            return InstallUtil::structure($post);
        }else if($step === 'database'){
            # 安装表数据
            return InstallUtil::database($post);
        }else if($step === 'config'){
            # 写入文件配置
            return InstallUtil::config($post);
        }else{
            # 安装失败
            return Json::fail('安装失败...');
        }
    }

    /**
     * 安装完成
     * @return string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function complete()
    {
        $data = [
            "desc"          => '恭喜你，安装完成~'
        ];
        return Json::successFul('安装成功',$data);
    }
}