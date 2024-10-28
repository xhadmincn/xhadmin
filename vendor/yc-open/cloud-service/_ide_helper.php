<?php

namespace YcOpen\CloudService {

    class Request
    {
        /**
         * 验证码相关接口
         * @access public
         * @return \YcOpen\CloudService\Request\CaptchaRequest
         */
        public static function Captcha()
        {
            /** @var \YcOpen\CloudService\Request\CaptchaRequest $instance */
            return $instance;
        }
        /**
         * 优惠券相关接口
         * @access public
         * @return \YcOpen\CloudService\Request\CouponRequest
         */
        public static function Coupon()
        {
            /** @var \YcOpen\CloudService\Request\CouponRequest $instance */
            return $instance;
        }
        /**
         * 登录相关接口
         * @access public
         * @return \YcOpen\CloudService\Request\LoginRequest
         */
        public static function Login()
        {
            /** @var \YcOpen\CloudService\Request\LoginRequest $instance */
            return $instance;
        }
        /**
         * 应用插件相关接口
         * @access public
         * @return \YcOpen\CloudService\Request\PluginRequest
         */
        public static function Plugin()
        {
            /** @var \YcOpen\CloudService\Request\PluginRequest $instance */
            return $instance;
        }
        /**
         * 应用插件分类相关接口
         * @access public
         * @return \YcOpen\CloudService\Request\PluginCateRequest
         */
        public static function PluginCate()
        {
            /** @var \YcOpen\CloudService\Request\PluginCateRequest $instance */
            return $instance;
        }
        /**
         * 网站相关接口
         * @access public
         * @return \YcOpen\CloudService\Request\SiteRequest
         */
        public static function Site()
        {
            /** @var \YcOpen\CloudService\Request\SiteRequest $instance */
            return $instance;
        }
        /**
         * 系统相关接口
         * @access public
         * @return \YcOpen\CloudService\Request\SystemUpdateRequest
         */
        public static function SystemUpdate()
        {
            /** @var \YcOpen\CloudService\Request\SystemUpdateRequest $instance */
            return $instance;
        }
        /**
         * 用户相关接口
         * @access public
         * @return \YcOpen\CloudService\Request\UserRequest
         */
        public static function User()
        {
            /** @var \YcOpen\CloudService\Request\UserRequest $instance */
            return $instance;
        }
        /**
         * 小程序相关接口
         * @access public
         * @return \YcOpen\CloudService\Request\MiniprojectRequest
         */
        public static function Miniproject()
        {
            /** @var \YcOpen\CloudService\Request\MiniprojectRequest $instance */
            return $instance;
        }
    }
}
