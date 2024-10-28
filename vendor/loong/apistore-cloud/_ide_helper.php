<?php

namespace loong\ApiStore\facade {
    /**
     * @see \loong\ApiStore\Http
     * @method \loong\ApiStore\Http setToken(string $token)
     * @package loong\ApiStore
     */
    class Http
    {
        /**
         * 验证码相关接口
         * @access public
         * @return \loong\ApiStore\Http
         */
        public static function setToken(string $token)
        {
            /** @var \loong\ApiStore\Http $instance */
            return $instance;
        }
    }
}
