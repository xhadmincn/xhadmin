<?php

namespace app\common\model;

use app\common\Model;

class SystemConfig extends Model
{
    // 设置JSON类型字段
	protected $json = ['value'];
    // 设置JSON数据返回数组
    protected $jsonAssoc = true;
}
