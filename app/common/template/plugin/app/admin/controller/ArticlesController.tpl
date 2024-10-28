<?php

namespace plugin\{TEAM_PLUGIN_NAME}\app\admin\controller;

use plugin\{TEAM_PLUGIN_NAME}\app\BaseController;
use app\common\trait\plugin\AriclesTrait;
use app\common\model\plugin\PluginArticles;
use support\Request;

/**
 * 文章系统
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-07
 */
class ArticlesController extends BaseController
{
    use AriclesTrait;

    /**
     * 应用ID（null则获取系统配置）
     * @var int|null
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $saas_appid = null;
    

    /**
     * 模型
     * @var PluginArticles
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $model = null;

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
