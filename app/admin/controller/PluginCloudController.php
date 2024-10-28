<?php

namespace app\admin\controller;

use app\common\BaseController;
use support\Request;
use YcOpen\CloudService\Cloud;
use YcOpen\CloudService\Request\CaptchaRequest;
use YcOpen\CloudService\Request\CouponRequest;
use YcOpen\CloudService\Request\LoginRequest;
use YcOpen\CloudService\Request\SiteRequest;
use YcOpen\CloudService\Request\UserRequest;

/**
 * 云服务中心
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-05-05
 */
class PluginCloudController extends BaseController
{
    /**
     * 云服务中心
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-06
     */
    public function index(Request $request)
    {
        $req = new UserRequest;
        $req->info();
        $cloud = new Cloud($req);
        $data  = $cloud->send();
        return $this->successRes($data->toArray());
    }

    /**
     * 云服务登录
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-06
     */
    public function login(Request $request)
    {
        $post = $request->post();
        if (!isset($post['username']) || !$post['username']) {
            return $this->fail('请输入云服务账号');
        }
        if (!isset($post['password']) || !$post['password']) {
            return $this->fail('请输入登录密码');
        }
        if (!isset($post['scode']) || !$post['scode']) {
            return $this->fail('请输入图像验证码');
        }
        if (empty($post['host'])) {
            return $this->fail('当前站点HOST未设置');
        }
        $req = new LoginRequest;
        $req->login();
        $req->setParams($post, null);
        $cloud = new Cloud($req);
        $data  = $cloud->send();
        $site  = new SiteRequest;
        $site->install();
        $site->domain = $post['host'];
        $site->title  = getHpConfig('web_name');
        $cloud        = new Cloud($site);
        $cloud->send();
        return $this->successFul('登录成功', $data->toArray());
    }


    /**
     * 获取账单流水
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-23
     * @param  Request $request
     * @return \support\Response
     */
    public function bill(Request $request)
    {
        $params = $request->get();
        $req    = new UserRequest;
        $req->getUserBill();
        $req->setQuery($params, null);
        $cloud = new Cloud($req);
        $data  = $cloud->send();
        return $this->successRes($data->toArray());
    }

    /**
     * 获取图像验证码
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-06
     */
    public function captcha(Request $request)
    {
        $req = new CaptchaRequest;
        $req->captchaCode();
        $req->bg = '255,255,255';
        $cloud   = new Cloud($req);
        $data    = $cloud->send();
        return $this->successRes($data->toArray());
    }
    /**
     * 获取可用优惠券
     * @param Request $request
     * @return \support\Response
     */
    public function getAvailableCoupon(Request $request)
    {
        $G   = $request->get();
        $req = new CouponRequest;
        $req->getAvailableCoupon();
        $req->setQuery($G, null);
        $cloud = new Cloud($req);
        $data  = $cloud->send();
        return $this->successRes($data->toArray());
    }
    /**
     * 获取优惠券列表
     * @param Request $request
     * @return \support\Response
     */
    public function getCouponList(Request $request)
    {
        $G   = $request->get();
        $req = new CouponRequest;
        $req->getCouponList();
        $req->setQuery($G, null);
        $cloud = new Cloud($req);
        $data  = $cloud->send();
        return $this->successRes($data->toArray());
    }
}