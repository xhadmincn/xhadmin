<?php

namespace app\common\model;

use app\common\Model;

class SystemAdminRole extends Model
{
    // 设置JSON字段转换
    protected $json = ['rule'];
    // 设置JSON数据返回数组
    protected $jsonAssoc = true;

    // 追加输出
    protected $append = [
        'rule_name'
    ];

    /**
     * 获取规则名称
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  type $value
     * @param  type $data
     * @return void
     */
    protected function getRuleNameAttr($value, $data)
    {
        if (!isset($data['rule'])) {
            return [];
        }
        $where = [
            ['path', 'in', $data['rule']],
        ];
        $list = SystemAuthRule::where($where)->column('title');
        return $list;
    }
}
