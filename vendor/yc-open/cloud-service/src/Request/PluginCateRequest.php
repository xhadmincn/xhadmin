<?php

namespace YcOpen\CloudService\Request;

use YcOpen\CloudService\Request;

/**
 * 应用插件分类相关接口
 * Class PluginRequest
 * @package YcOpen\CloudService\Request
 */
class PluginCateRequest extends Request
{
    /**
     * 获取插件列表
     * @param mixed $query
     * @return PluginRequest
     */
    public function index(mixed $query = null)
    {
        $this->setUrl('PluginCate/index');
        if ($query) {
            $this->setQuery($query);
        }
        return $this;
    }
}
