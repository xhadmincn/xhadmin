<?php

namespace app\common\builder\components;

use app\common\builder\FormBuilder;
use think\Validate;

/**
 * 表单验证
 * @author 贵州猿创科技有限公司
 * @copyright (c) 2023
 */
trait FormValidate
{
    /**
     * 表单验证
     * @param mixed $validate
     * @return FormBuilder
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function formValidate($validate): FormBuilder
    {
        /**
         * 实例验证类
         * @var Validate
         */
        $class = new $validate;
        return $this;
    }
}