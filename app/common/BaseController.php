<?php

namespace app\common;

use app\BaseController as Controller;
use app\common\utils\Json;
use app\common\Model;
use support\Request;
use think\App;

/**
 * @title 控制器基类
 * @desc 控制器描述
 * @author 楚羽幽 <admin@hangpu.net>
 */
class BaseController extends Controller
{
    // 使用JSON工具类
    use Json;

    /**
     * @var Model
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-13
     */
    protected $model;

    /**
     * 模块名
     * @var string
     */
    protected $moduleName;

    
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 构造函数
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        # 请求信息
        $request    = $app->request;
        $this->moduleName = trim($request->root(), '/');
    }

    /**
     * 获取地址栏参数后，返回查询相应参数
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-04
     * @param  Request $request
     * @return array
     */
    protected function getParams(Request $request): array
    {
        if (!$request) {
            $request = request();
        }
        $params = $request->get();
        // 关联预查询
        $with = [];
        if (isset($params['with'])) {
            $with = explode(',', $params['with']);
            unset($params['with']);
        }
        // 数据排序
        $orderBy = [];
        if (isset($params['orderBy'])) {
            $orderBy = $params['orderBy'];
            unset($params['orderBy']);
        }
        // 获取数量
        $limit = 15;
        if (isset($params['limit'])) {
            $limit = $params['limit'];
            unset($params['limit']);
        }
        // 当前分页
        $page = 1;
        if (isset($params['page'])) {
            $page = $params['page'];
            unset($params['page']);
        }
        // 组装查询条件
        $where = [];
        foreach ($params as $field => $value) {
            if ($value) {
                if (is_array($value)) {
                    foreach ($value as $condition => $val) {
                        if ($condition == 'like') {
                            $where[] = [$field, $condition, "%{$val}%"];
                        } else {
                            $where[] = [$field, $condition, $val];
                        }
                    }
                } else {
                    $inValue = explode(',', $value);
                    if (count($inValue) > 1) {
                        $where[] = [$field, 'in', $inValue];
                    } else {
                        $where[] = [$field, '=', $value];
                    }
                }
            }
        }

        // 返回参数
        return [$where, $orderBy, $limit, $page, $with];
    }

    /**
     * 设置开关值
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-01
     */
    public function rowEdit(Request $request)
    {
        # 主键名称
        $keyField = $request->post('keyField');
        # 主键值
        $id = $request->post($keyField);
        # 字段名
        $field = $request->post('field');
        # 数据值
        $value = $request->post('value');
        $where = [
            $keyField => $id
        ];
        $model = $this->model;
        $model = $model->where($where)->find();
        $model->$field = $value;
        if (!$model->save()) {
            return Json::fail('修改失败');
        }
        return Json::success('修改成功');
    }
}
