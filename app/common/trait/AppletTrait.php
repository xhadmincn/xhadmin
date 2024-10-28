<?php
namespace app\common\trait;

use app\common\trait\applet\DyMiniTrait;
use app\common\trait\applet\WxMiniTrait;
use support\Request;

/**
 * 小程序接管类
 * @author 贵州猿创科技有限公司
 * @copyright (c) 贵州猿创科技有限公司
 */
trait AppletTrait
{
    # 微信小程序接管类
    use WxMiniTrait;
    # 抖音小程序接管类
    use DyMiniTrait;

    /**
     * 请求对象
     * @var Request
     */
    protected $request = null;

    /**
     * 应用ID（null则获取系统配置）
     * @var int|null
     */
    protected $saas_appid = null;
}