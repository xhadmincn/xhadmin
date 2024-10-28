<?php

namespace app\store\controller;

use app\common\builder\ListBuilder;
use app\common\BaseController;
use app\common\model\StoreLog;
use support\Request;

/**
 * 管理员列表
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-08
 */
class StoreLogController extends BaseController
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
        $data = $builder
            ->addScreen('keyrowds', 'input', '路由搜索')
            ->addScreen('ip', 'input', 'IP')
            ->addTopButton('clear15', '清空15天以上', [
                'type'          => 'confirm',
                'api'           => 'store/StoreLog/clear',
                'path'          => '/StoreLog/clear',
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
                'api'           => 'store/StoreLog/clear',
                'path'          => '/StoreLog/clear',
                'queryParams'         => [
                    'day'       => 30
                ]
            ], [
                'type'          => 'danger',
                'content'       => '确定清空30天以上的日志吗？'
            ], [
                'type'          => 'danger'
            ])
            ->pageConfig()
            ->addColumn('id', '序号', [
                'width'         => 100
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
        $where[] = ['store_id', '=', $request->user['id']];
        $ip = $request->get('ip');
        if ($ip) {
            $where[] = ['action_ip', 'like', "%{$ip}%"];
        }
        $keyrowds = $request->get('keyrowds');
        if ($keyrowds) {
            $where[] = ['path', 'like', "%{$keyrowds}%"];
        }
        $data = StoreLog::where($where)
            ->order('id', 'desc')
            ->paginate()->each(function ($item) {
                $item->params = json_encode($item->params, JSON_UNESCAPED_UNICODE);
                return $item;
            });
        return parent::successRes($data);
    }
    public function clear(Request $request)
    {
        $day = $request->param('day');
        $where = [];
        $where[] = ['store_id', '=', $request->user['id']];
        $where[] = ['create_at', '<', date('Y-m-d H:i:s', strtotime("-{$day} day"))];
        StoreLog::where($where)->delete();
        return parent::success('清空成功');
    }
}
