<?php
namespace app\common\service;

use app\common\manager\DbMgr;
use app\common\utils\DirUtil;
use support\Request;
use think\facade\Log;
use app\common\model\StorePlugins;
use app\common\model\StoreApp;

/**
 * 执行数据同步更新
 * @author 贵州猿创科技有限公司
 * @copyright (c) 贵州猿创科技有限公司
 */
class UpdateDataService extends SystemUpdateService
{
    /**
     * 客户端版本号
     * @var int
     */
    protected $clientVersion = null;
    /**
     * 本地版本名称
     * @var string
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $clientVersionName = '';

    /**
     * 构造函数
     * @param \support\Request $request
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function __construct(Request $request,$clientVersion,$clientVersionName='')
    {
        $this->clientVersion = $clientVersion;
        $this->clientVersionName = $clientVersionName;
        parent::__construct($request);
    }

    /**
     * 前置更新
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function beforeUpdate()
    {
        # sql目录
        $sqlDir = root_path().'update';
        if (!is_dir($sqlDir)) {
            return [];
        }
        # 获取sql文件
        $sqlFiles = glob("{$sqlDir}/*.sql");
        $data      = [];
        foreach ($sqlFiles as $file) {
            $sqlContent = file_get_contents($file);
            if (empty($sqlContent) && file_exists($file)) {
                unlink("{$sqlDir}/{$file}");
                continue;
            }
            $data[] = [
                'file' => $file,
                'sql'  => $sqlContent,
            ];
        }
        return $data;
    }

    /**
     * 后置更新
     * @param mixed $data
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function update($data)
    {
        $prefix     = config('database.connections.mysql.prefix');
        $prefixs    = ['`php_', '`yc_','`__PREFIX__'];
        try {
            # 连接原生PDO
            $pdo = DbMgr::instance()->getPdo();
            foreach ($data as $value) {
                if (empty($value['file'])) {
                    continue;
                }
                if (empty($value['sql'])) {
                    continue;
                }
                try {
                    # 替换前缀
                    $sql = str_replace($prefixs, "`{$prefix}", $value['sql']);
                    Log::info("更新系统SQL：{$sql}");
                    # 执行SQL
                    $pdo->exec($sql);
                } catch (\Throwable $e) {
                    Log::error("系统更新SQL错误，继续执行：{$e->getMessage()}，Line：{$e->getLine()}，File：{$e->getFile()}");
                }
                # 执行后，无论成功失败，删除文件
                file_exists($value['file']) && unlink($value['file']);
            }
            # 检测目录是否为空，为空则删除
            $updateDir = root_path().'/update';
            if (DirUtil::isDirEmpty($updateDir)) {
                DirUtil::delDir($updateDir);
            }
        } catch (\Throwable $e) {
            Log::error("更新SQL错误，终止：{$e->getMessage()}，Line：{$e->getLine()}，File：{$e->getFile()}");
        }
        try {
            $use_auth_num=[];
            $use_plugin_num=[];
            $useAuthNum=StorePlugins::select();
            foreach ($useAuthNum as $key => $value) {
                if(!isset($use_auth_num[$value['plugin_name']]))$use_auth_num[$value['plugin_name']]=0;
                $use_auth_num[$value['plugin_name']]+=$value['auth_num'];
            }
            $usePluginNum=StoreApp::select();
            foreach ($usePluginNum as $key => $value) {
                if(!isset($use_plugin_num[$value['name']]))$use_plugin_num[$value['name']]=0;
                $use_plugin_num[$value['name']]++;
            }
            $data=[
                'name'=>'plugins',
                'version'=>$this->clientVersion,
                'version_name'=>$this->clientVersionName,
                'use_auth_num'=>$use_auth_num,
                'use_plugin_num'=>$use_plugin_num,
            ];
            \YcOpen\CloudService\Request::Plugin()->authNum($data)->response();
        } catch (\Throwable $th) {
        }
    }
}