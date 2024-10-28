<?php

namespace app\admin\controller;

use app\admin\model\SystemAdmin;
use app\common\builder\ListBuilder;
use app\common\BaseController;
use app\common\model\SystemAdminLog;
use support\Request;
use Swoole\Coroutine\System;

/**
 * 管理员列表
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-08
 */
class SystemAdminLogController extends BaseController
{
    /**
     * 获取表格
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function indexGetTable(Request $request)
    {
        $builder = new ListBuilder;
        $Admin = SystemAdmin::field('id as value,nickname as label')->select();
        $data = $builder
            ->pageConfig()
            ->addTopButton('clear15', '清空15天以上', [
                'type'          => 'confirm',
                'api'           => 'admin/SystemAdminLog/clear',
                'path'          => '/SystemAdminLog/clear',
                'queryParams'         => [
                    'day'       => 15
                ]
            ], [
                'type'          => 'danger',
                'content'       => '确定清空15天以上的日志吗？'
            ], [
                'type'          => 'danger'
            ])
            ->addTopButton('clear30', '清空30天以上', [
                'type'          => 'confirm',
                'api'           => 'admin/SystemAdminLog/clear',
                'path'          => '/SystemAdminLog/clear',
                'queryParams'         => [
                    'day'       => 30
                ]
            ], [
                'type'          => 'danger',
                'content'       => '确定清空30天以上的日志吗？'
            ], [
                'type'          => 'danger'
            ])
            ->addScreen('keyrowds', 'input', '路由搜索')
            ->addScreen('ip', 'input', 'IP')
            ->addScreen('admin_id', 'select', '管理员', [
                'options' => $Admin
            ])
            ->addColumn('id', '序号', [
                'width'         => 100
            ])
            ->addColumn('username', '账号', [
                'width'         => 100
            ])
            ->addColumn('nickname', '昵称', [
                'width'         => 100
            ])
            ->addColumnEle('headimg', '头像', [
                'width'         => 100,
                'params'        => [
                    'type'      => 'image',
                ],
            ])
            ->addColumn('path', '路由')
            ->addColumn('params', '参数')
            ->addColumn('action_ip', 'IP', [
                'width'         => 180
            ])
            ->addColumn('create_at', '访问时间', [
                'width'         => 180
            ])
            ->create();
        return parent::successRes($data);
    }

    /**
     * 列表
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function index(Request $request)
    {
        $where = [];
        $admin_id = $request->get('admin_id');
        if ($admin_id) {
            $where[] = ['log.admin_id', '=', $admin_id];
        }
        $ip = $request->get('ip');
        if ($ip) {
            $where[] = ['log.action_ip', 'like', "%{$ip}%"];
        }
        $keyrowds = $request->get('keyrowds');
        if ($keyrowds) {
            $where[] = ['log.path', 'like', "%{$keyrowds}%"];
        }
        $data = SystemAdminLog::alias('log')->where($where)
            ->join('system_admin admin', 'log.admin_id = admin.id', 'LEFT')
            ->field('log.*,admin.username,admin.nickname,admin.headimg')
            ->order('log.id', 'desc')
            ->paginate()->each(function ($item) {
                $item->params = json_encode($item->params, JSON_UNESCAPED_UNICODE);
                return $item;
            });
        return parent::successRes($data);
    }
    public function clear(Request $request)
    {
        $day = $request->get('day');
        $time = time() - $day * 86400;
        SystemAdminLog::whereTime('create_at', '<', $time)->delete();
        return parent::success('清空成功');
    }
}
