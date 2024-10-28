<?php

namespace app\store\service\develop;
use app\common\manager\StoreAppMgr;

/**
 * 应用插件安装
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
trait CreateProject
{
    /**
     * 创建项目数据
     * @param array $data
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function createProject(array $data)
    {
        $store_id = request()->user['id'];
        $data     = [
            'store_id'      => $store_id,
            'title'         => $data['title'],
            'name'          => $this->teamPluginName,
            'status'        => '20',
            'platform'      => $data['platform'],
            'username'      => $data['username'],
            'password'      => $data['password'],
            'logo'          => $data['logo']
        ];
        # 创建项目
        StoreAppMgr::created($data);
    }
}