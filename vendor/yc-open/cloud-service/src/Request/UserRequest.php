<?php

namespace YcOpen\CloudService\Request;

use YcOpen\CloudService\Request;

/**
 * 用户相关接口
 * Class UserRequest
 * @package YcOpen\CloudService\Request
 */
class UserRequest extends Request
{
    /**
     * 获取用户信息
     * @param mixed $query
     * @return UserRequest
     */
    public function info(mixed $query = null)
    {
        $this->setUrl('User/info');
        if ($query) {
            $this->setQuery($query);
        }
        return $this;
    }
    /**
     * 获取用户账单
     * @param mixed $query
     * @return UserRequest
     */
    public function getUserBill(mixed $query = null)
    {
        $this->setUrl('User/getUserBill');
        if ($query) {
            $this->setQuery($query);
        }
        return $this;
    }
}
