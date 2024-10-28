<?php

namespace app\common\manager;

use app\common\model\Store;
use app\common\model\SystemConfig;
use app\common\model\SystemUpload;
use app\common\model\SystemUploadCate;
use Exception;
use think\facade\Db;

class StoreMgr
{
    /**
     * 获取站点数据模型
     * @param array $where
     * @return \app\common\model\Store
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function model(array $where): Store
    {
        $model = Store::where($where)->find();
        if (!$model) {
            throw new Exception('找不到该站点数据');
        }
        return $model;
    }

    /**
     * 获取站点数据
     * @param array $where
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function detail(array $where): array
    {
        return self::model($where)->toArray();
    }

    /**
     * 删除项目
     * @param array $where
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function del(array $where)
    {
        if (empty($where['id'])) {
            throw new Exception('参数错误--[站点ID]');
        }
        Db::startTrans();
        try {
            # 获取项目
            $model = self::model($where);
            # 删除站点项目
            # 删除站点附件
            // SystemUpload::where($whereOther)->delete();
            // SystemUploadCate::where($whereOther)->delete();
            # 删除项目
            if (!$model->delete()) {
                throw new Exception('删除失败');
            }
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            throw $e;
        }
    }
}
