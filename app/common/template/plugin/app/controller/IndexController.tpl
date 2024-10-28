<?php

namespace plugin\{TEAM_PLUGIN_NAME}\app\controller;

use plugin\{TEAM_PLUGIN_NAME}\app\BaseController;
use support\Request;

/**
 * 默认控制器
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-07
 */
class IndexController extends BaseController
{
    /**
     * 不需要登录的方法
     * @var string[]
     */
    protected $noNeedLogin = ["index"];

    /**
     * 应用ID（null则获取系统配置）
     * @var int|null
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $saas_appid = null;

    /**
     * 构造函数
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function initialize()
    {
        parent::initialize();
        $this->saas_appid = $this->request->saas_appid;
    }

    
    /**
     * 默认首页
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function index()
    {
        return 'Hello，{TEAM_PLUGIN_NAME}';
    }
}
