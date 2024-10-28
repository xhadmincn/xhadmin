<?php

namespace plugin\{TEAM_PLUGIN_NAME}\app\admin\controller;

use plugin\{TEAM_PLUGIN_NAME}\app\BaseController;
use app\common\trait\UploadTrait;
use support\Request;

/**
 * 附件库
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-07
 */
class UploadController extends BaseController
{
    use UploadTrait;

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
}
