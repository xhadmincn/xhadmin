<?php

namespace plugin\{TEAM_PLUGIN_NAME}\app\admin\controller;

use app\common\BaseController;
use app\common\trait\AppletTrait;

/**
 * 小程序管理器
 * @author 贵州猿创科技有限公司
 * @copyright (c) 贵州猿创科技有限公司
 */
class AppletController extends BaseController
{
    # 使用管理器trait
    use AppletTrait;

    /**
     * 应用ID（null则获取系统配置）
     * @var int|null
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $saas_appid = null;

    public function initialize()
    {
        parent::initialize();
        $this->saas_appid = $this->request->saas_appid;
    }
}
