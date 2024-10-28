<?php

namespace plugin\{TEAM_PLUGIN_NAME}\app\admin\controller;

use plugin\{TEAM_PLUGIN_NAME}\app\BaseController;
use support\Request;

/**
 * 默认首页
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-07
 */
class IndexController extends BaseController
{
    /**
     * 无需验证APPID方法
     * @var array
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $noNeedAppid = ['index'];

    /**
     * 无需登录方法
     * @var array
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $noNeedLogin = ['index'];

    /**
     * 无需鉴权方法
     * @var array
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $noNeedAuth = ['index'];

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
     * 渲染后台
     * @param \support\Request $request
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function index(Request $request)
    {
        return getAdminView($request->plugin);
    }
}
