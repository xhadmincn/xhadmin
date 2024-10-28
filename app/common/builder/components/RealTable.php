<?php

namespace app\common\builder\components;

/**
 * 实时表格
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
trait RealTable
{
    /**
     * 实时表格
     * @var array
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $realTable    = [
        # 是否开启实时表格
        'enable'            => false,
        # 表格实时刷新时间间隔（单位：秒）
        'time_interval'     => 10,
    ];

    /**
     * 开启实时表格
     * @param bool $enable
     * @param int $time_interval
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function realTable(bool $enable = true, int $time_interval = 10)
    {
        $this->realTable['enable'] = $enable;
        $this->realTable['time_interval'] = $time_interval;
        return $this;
    }
}