<?php

namespace app\controller;

use app\common\BaseController;
use app\common\manager\StoreAppMgr;
use Exception;
use support\Request;
use think\facade\View;

class TagsController extends BaseController
{
    /**
     * 单页详情
     * @param \support\Request $request
     * @return string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function index(Request $request)
    {
        $saas_appid = $request->get('appid', '');
        $name       = $request->get('name', '');
        if (empty($saas_appid) || empty($name)) {
            throw new Exception('参数错误');
        }
        $request->appid = $saas_appid;
        $storeApp = StoreAppMgr::detail(['id' => $saas_appid]);
        if (empty($storeApp)) {
            throw new Exception('应用不存在');
        }
        $model = new \app\common\model\plugin\PluginTags;
        $where = [
            'name' => $name,
        ];
        $model = $model->where($where)->find();
        if (empty($model)) {
            throw new Exception('单页内容不存在');
        }
        if ($model->status !== '20') {
            throw new Exception('单页内容已禁用');
        }
        return View::fetch('tags/index', [
            'detail'    => $model,
            'storeApp'  => $storeApp,
        ]);
    }
}
