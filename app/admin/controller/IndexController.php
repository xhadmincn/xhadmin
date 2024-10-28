<?php

namespace app\admin\controller;

use app\common\model\StoreApp;
use app\common\service\SystemInfoService;
use app\common\BaseController;
use app\common\enum\PlatformTypes;
use support\Request;
use think\facade\Cache;

/**
 * 首页数据
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-05-15
 */
class IndexController extends BaseController
{
    /**
     * 渲染后台视图
     * @return \think\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function index()
    {
        return getAdminView();
    }

    /**
     * 首页数据统计
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-12
     */
    public function consoleCount(Request $request)
    {
        // 获取系统信息
        $teamInfo = SystemInfoService::info();
        $inKeys = array_keys($teamInfo);
        $teamFields = [
            'about_name'        => '研发企业',
            'ecology'           => '系统生态',
            'fream_version'     => '框架版本',
            'service_wx'        => '微信咨询',
        ];
        $team = [];
        foreach ($teamFields as $field => $label) {
            if (in_array($field, $inKeys)) {
                $team[] = [
                    'title' => $label,
                    'values' => $teamInfo[$field],
                ];
            }
        }

        // 获取产品动态
        $product = [];

        // 应用总数量
        $platformTypes = PlatformTypes::toArray();
        $platformApp = [];
        $platform_echarts = [];
        $StoreApp = StoreApp::select();
        $count = [];
        foreach ($StoreApp as $item) {
            foreach ($item->platform as $platform) {
                if (isset($count[$platform])) {
                    $count[$platform]++;
                } else {
                    $count[$platform] = 1;
                }
            }
        }
        foreach ($platformTypes as $value) {
            $where = [
                ['platform', 'like', "%{$value['value']}%"],
            ];
            $platformApp[$value['value']] = $count[$value['value']] ?? 0;

            // 查询图表数据
            $today = StoreApp::where($where)->whereDay('create_at')->count();
            $week = StoreApp::where($where)->whereWeek('create_at')->count();
            $moon = StoreApp::where($where)->whereMonth('create_at')->count();
            $quarter = StoreApp::where($where)->whereTime('create_at', '-3 month')->count();
            $half_year = StoreApp::where($where)->whereTime('create_at', '-6 month')->count();
            $year = StoreApp::where($where)->whereYear('create_at')->count();
            // 组装图表数据
            $platform_echarts[] = [
                'name' => $value['text'],
                'type' => 'line',
                'stack' => 'Total',
                'smooth' => true,
                'data' => [
                    $today,
                    #今日
                    $week,
                    #本周
                    $moon,
                    #本月
                    $quarter,
                    #季度
                    $half_year,
                    #半年
                    $year,
                    #今年
                ],
            ];
        }
        // 获取队列信息
        $taskQueue = '';
        if (!Cache::has('xhadmin_task')) {
            $taskQueue = "当前系统未启动队列\n请执行命令：php think xhadmin start -d\n\n如使用 supervisor 管理软件\n请使用该命令：php think xhadmin start";
        }

        $data = [
            'team'                  => $team,
            'product'               => $product,
            'platformApp'           => $platformApp,
            'taskQueue'             => $taskQueue,
            'platform_echarts'      => $platform_echarts
        ];
        return $this->successRes($data);
    }

    /**
     * 清理服务端缓存
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public function clear(Request $request)
    {
        // 预留扩展（后续使用）
        return parent::success('缓存清理成功');
    }

    /**
     * 解除UI锁定
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public function lock(Request $request)
    {
        // 预留扩展（后续使用）
        return parent::success('解锁成功');
    }
}
