<?php

namespace app\controller;

use app\common\BaseController;
use app\common\manager\StoreAppMgr;
use app\common\model\plugin\PluginArticles;
use app\common\model\plugin\PluginArticlesCate;
use Exception;
use support\Request;
use think\facade\View;

class ArticlesController extends BaseController
{
    /**
     * 文章列表
     * @param \support\Request $request
     * @return string
     * @author John
     */
    public function index(Request $request)
    {
        $appid = $request->get('appid', '');
        if (empty($appid)) {
            throw new Exception('参数错误');
        }
        $where = [
            'saas_appid'    => $appid,
            'status'        => '20'
        ];
        $data  = PluginArticlesCate::where($where)->order('sort desc,id desc')->select()
        ->each(function ($item) {
            $where = [
                'cid'           => $item['id'],
                'saas_appid'    => $item['saas_appid'],
                'status'        => '20'
            ];
            $item['artcles'] = PluginArticles::where($where)->order('view desc,id desc')->paginate();
            return $item;
        });
        # 渲染视图
        return View::fetch('articles/index',[
            'data'      => $data,
        ]);
    }

    /**
     * 分类下全部文章
     * @param \support\Request $request
     * @return string
     * @author John
     */
    public function category(Request $request)
    {
        $appid = $request->get('appid', '');
        $cid = $request->get('cid', '');
        if (empty($cid)) {
            throw new Exception('参数错误');
        }
        $where = [
            'id'            => $cid,
            'saas_appid'    => $appid,
        ];
        $data = PluginArticlesCate::where($where)->find();
        if (!$data) {
            throw new Exception('分类不存在');
        }
        if ($data['status'] !== '20') {
            throw new Exception('分类已禁用');
        }
        $where           = [
            'cid'           => $cid,
            'saas_appid'    => $appid,
            'status'        => '20'
        ];
        $data['artcles'] = PluginArticles::where($where)->order('view desc,id desc')->paginate();
        # 渲染视图
        return View::fetch('articles/category',[
            'data'      => $data,
        ]);
    }

    /**
     * 文章详情
     * @param \support\Request $request
     * @throws \Exception
     * @return string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function detail(Request $request)
    {
        $saas_appid = $request->get('appid', '');
        $aid       = $request->get('aid', '');
        if (empty($saas_appid) || empty($aid)) {
            throw new Exception('参数错误');
        }
        $request->appid = $saas_appid;
        $storeApp = StoreAppMgr::detail(['id' => $saas_appid]);
        if (empty($storeApp)) {
            throw new Exception('应用不存在');
        }
        $where = [
            'id'            => $aid,
            'saas_appid'    => $saas_appid,
        ];
        $model = new PluginArticles;
        $model = $model->where($where)->find();
        if (empty($model)) {
            throw new Exception('文章内容不存在');
        }
        if ($model->status !== '20') {
            throw new Exception('文章已被禁用');
        }
        # 浏览数+1
        $model->setInc('view');
        # 渲染视图
        return View::fetch('articles/detail', [
            'detail'    => $model,
            'storeApp'  => $storeApp,
        ]);
    }
}
