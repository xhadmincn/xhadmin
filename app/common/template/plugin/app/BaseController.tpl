<?php

namespace plugin\{TEAM_PLUGIN_NAME}\app;

use app\common\BaseController as Controller;

/**
 * 基类控制器
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-07
 */
class BaseController extends Controller
{
    /**
     * 插件前缀
     * @var string
     */
    protected $pluginPrefix = null;

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
        $this->pluginPrefix = "app/{$this->request->plugin}";
    }
}
