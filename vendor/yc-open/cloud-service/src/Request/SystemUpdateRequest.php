<?php

namespace YcOpen\CloudService\Request;

use YcOpen\CloudService\Request;
use YcOpen\CloudService\Validator;

/**
 * 系统更新相关接口
 * Class SystemUpdateRequest
 * @package YcOpen\CloudService\Request
 */
class SystemUpdateRequest extends Request
{
    /**
     * 验证是否需要更新
     * @param mixed $query
     * @return SystemUpdateRequest
     */
    public function verify(mixed $query = null)
    {
        $this->setUrl('SystemUpdate/verify');
        $validator = new Validator;
        $validator->rules([
            'version_name' => 'required',
            'version' => 'required'
        ]);
        $this->validator = $validator;
        if ($query) {
            $this->setQuery($query);
        }
        return $this;
    }
    /**
     * 获取插件详情
     * @param mixed $query
     * @return SystemUpdateRequest
     */
    public function detail(mixed $query = null)
    {
        $this->setUrl('SystemUpdate/detail');
        $validator = new Validator;
        $validator->rules([
            'version_name' => 'required',
            'version' => 'required'
        ]);
        $this->validator = $validator;
        if ($query) {
            $this->setQuery($query);
        }
        return $this;
    }
    /**
     * 获取最新版
     * @param mixed $query
     * @return SystemUpdateRequest
     */
    public function newVersion(mixed $query = null)
    {
        $this->setUrl('SystemUpdate/newVersion');
        if ($query) {
            $this->setQuery($query);
        }
        return $this;
    }
    /**
     * 获取版本列表
     * @param mixed $query
     * @return SystemUpdateRequest
     */
    public function list(mixed $query = null)
    {
        $this->setUrl('SystemUpdate/list');
        if ($query) {
            $this->setQuery($query);
        }
        return $this;
    }
    /**
     * 获取插件密钥
     * @param mixed $query
     * @return SystemUpdateRequest
     */
    public function getKey(mixed $query = null)
    {
        $this->setUrl('SystemUpdate/getKey');
        $validator = new Validator;
        $validator->rules([
            'target_version' => 'required'
        ]);
        $this->validator = $validator;
        if ($query) {
            $this->setQuery($query);
        }
        return $this;
    }
}
