<?php
namespace app\common\process;
use app\common\model\StorePlugins;
use app\common\model\StorePluginsExpire;
use think\facade\Db;

class CreateTask
{
    public function run()
    {
        try {
            $StorePluginsExpire = StorePluginsExpire::whereTime('expire_time', '<', strtotime("-1 hours"))->limit(10)->select();
            foreach ($StorePluginsExpire as $item) {
                Db::startTrans();
                try {
                    StorePluginsExpire::where(['id'=>$item->id])->delete();
                    $useAuthNum=StorePluginsExpire::where(['store_plugins_id'=>$item->store_plugins_id])->sum('auth_num');
                    $storePlugins = StorePlugins::where(['id'=>$item->store_plugins_id])->find();
                    $storePlugins->auth_num = $useAuthNum;
                    $storePlugins->save();
                    Db::commit();
                } catch (\Throwable $th) {
                    Db::rollback();
                }
            }
        } catch (\Throwable $th) {
            p([$th->getMessage(), $th->getLine(), $th->getFile()]);
        }
    }
}
