<?php

namespace YcOpen\CloudService\Request;

use YcOpen\CloudService\Request;
use YcOpen\CloudService\Validator;

/**
 * 应用插件相关接口
 * Class PluginRequest
 * @package YcOpen\CloudService\Request
 */
class PluginRequest extends Request
{
    /**
     * 获取插件列表
     * @param mixed $query
     * @return PluginRequest
     */
    public function list(mixed $query = null)
    {
        $this->setUrl('Plugin/list');
        if ($query) {
            $this->setQuery($query);
        }
        return $this;
    }
    /**
     * 获取插件详情
     * @param mixed $query
     * @return PluginRequest
     */
    public function detail(mixed $query = null)
    {
        $this->setUrl('Plugin/detail');
        $validator = new Validator;
        $validator->rules([
            'name' => 'required'
        ]);
        $this->validator = $validator;
        if ($query) {
            $this->setQuery($query);
        }
        return $this;
    }
    /**
     * 购买插件
     * @param mixed $query
     * @return PluginRequest
     */
    public function buy(mixed $query = null)
    {
        $this->setUrl('Plugin/buy');
        $validator = new Validator;
        $validator->rules([
            'name' => 'required'
        ]);
        $this->validator = $validator;
        if ($query) {
            $this->setQuery($query);
        }
        return $this;
    }
    /**
     * 获取插件密钥
     * @param mixed $query
     * @return PluginRequest
     */
    public function getKey(mixed $query = null)
    {
        $this->setUrl('Plugin/getKey');
        $validator = new Validator;
        $validator->rules([
            'name' => 'required',
            'version' => 'required',
            'saas_version' => 'required',
            'local_version' => 'required'
        ]);
        $this->validator = $validator;
        if ($query) {
            $this->setQuery($query);
        }
        return $this;
    }
    public function authNum(mixed $params=null)
    {
        $this->setMethod('POST');
        $this->setUrl('Plugin/authNum');
        $validator = new Validator;
        $validator->rules([
            'name' => 'required'
        ]);
        $this->validator = $validator;
        if ($params) {
            $this->setParams($params);
        }
        return $this;
    }
}
