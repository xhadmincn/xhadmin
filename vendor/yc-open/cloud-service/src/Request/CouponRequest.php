<?php

namespace YcOpen\CloudService\Request;

use YcOpen\CloudService\Request;
use YcOpen\CloudService\Validator;

/**
 * 优惠券相关接口
 * Class CouponRequest
 * @package YcOpen\CloudService\Request
 */
class CouponRequest extends Request
{
    /**
     * 获取可用优惠券
     * @param mixed $query
     * @return CouponRequest
     */
    public function getAvailableCoupon(mixed $query = null)
    {
        $this->setUrl('Coupon/getAvailableCoupon');
        $validator = new Validator;
        $validator->rules([
            'type' => 'required'
        ]);
        $this->validator = $validator;
        if ($query) {
            $this->setQuery($query);
        }
        return $this;
    }
    /**
     * 获取优惠券列表
     * @param mixed $query
     * @return CouponRequest
     */
    public function getCouponList(mixed $query = null)
    {
        $this->setUrl('Coupon/getCouponList');
        if ($query) {
            $this->setQuery($query);
        }
        return $this;
    }
}
