<?php

namespace YcOpen\CloudService\Request;

use YcOpen\CloudService\Request;

/**
 * 验证码相关接口
 * Class CaptchaRequest
 * @package YcOpen\CloudService\Request
 */
class CaptchaRequest extends Request
{
    /**
     * 获取验证码
     * @param mixed $query
     * @return CaptchaRequest
     */
    public function captchaCode(mixed $query = null)
    {
        $this->setUrl('Captcha/captchaCode');
        if ($query) {
            $this->setQuery($query);
        }
        return $this;
    }
}
