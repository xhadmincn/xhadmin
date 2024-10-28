<?php

namespace app\controller;

use app\common\BaseController;
use support\Request;

/**
 * 支付宝支付回调通知
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class AlipayController extends BaseController
{
    /**
     * 入口
     * @param \support\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function index(Request $request)
    {
        return $this->success('欢迎使用');
    }
}
