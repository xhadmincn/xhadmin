<?php

namespace app\common\service;

use app\common\exception\RollBackException;
use app\common\manager\ZipMgr;
use app\common\manager\JsonMgr;
use app\common\utils\DirUtil;
use Exception;
use support\Request;
use think\facade\Log;
use YcOpen\CloudService\Request as CloudRequest;
use YcOpen\CloudService\Cloud;
use YcOpen\CloudService\Request\SystemUpdateRequest;
use zjkal\MysqlHelper;

/**
 * 更新服务类
 * 更新步骤如下：
 * 1、下载更新包
 * 2、备份代码
 * 3、备份数据库
 * 4、解压更新包-删除代码-复制已解压文件至目标路径
 * 5、执行数据同步更新（引入最新的更新类）
 * 6、更新成功
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class SystemUpdateService
{
    /**
     * 当前请求管理器
     * @var Request
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $request = null;

    /**
     * 本地版本名称
     * @var string
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $clientVersionName = '';

    /**
     * 本地版本号
     * @var int
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $clientVersion = 0;

    /**
     * 临时ZIP文件路径
     * @var string
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $tempZipFilePath = null;

    /**
     * 备份源代码路径
     * @var string
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $backupCodePath = null;

    /**
     * 备份源数据库路径
     * @var string
     */
    protected $backupSqlPath = null;

    /**
     * 备份覆盖代码路径
     * @var 
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $backCoverPath = null;

    /**
     * 目标路径
     * @var string
     */
    protected $targetPath = null;


    /**
     * 打包时忽略文件或目录列表
     * 删除时忽略以下目录或文件
     * @var array
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $ignoreList = [
        '.git',
        'backup',
        'update',
        'public/upload',
        'public/uploads',
        'plugin',
        'runtime',
        'kfadmin-backup.sql',
        'kfadmin-backup.zip',
    ];

    /**
     * 备份需要覆盖的文件
     * @var array
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $backCoverList = [
        '.env',
    ];

    /**
     * 构造函数
     * @param \support\Request $Request
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function __construct(Request $Request)
    {
        # 设置请求实体
        $this->request = $Request;

        # 本地版本信息
        $systemInfo = SystemInfoService::info();
        # 客户端版本号
        $this->clientVersion = $systemInfo['system_version'];
        # 客户端版本名称
        $this->clientVersionName = $systemInfo['system_version_name'];

        # 下载框架更新包临时地址
        $this->tempZipFilePath = runtime_path() . "core/xhadmin-update.zip";
        # 检测核心目录不存在则创建
        if (!is_dir(dirname($this->tempZipFilePath))) {
            mkdir(dirname($this->tempZipFilePath), 0777, true);
        }
        # 解压至目标地址(根据环境变量设置)
        if (!env('APP_DEBUG', true)) {
            # 生产环境
            $rootPath = substr(root_path(), 0, -1);
            $this->targetPath = $rootPath;
        } else {
            # 开发环境
            $this->targetPath = runtime_path() . 'web';
            if (!is_dir($this->targetPath)) {
                mkdir($this->targetPath, 0777, true);
            }
        }
        # 备份当前版本代码地址
        $this->backupCodePath = root_path() . "backup/xhadmin-backup-{$this->clientVersion}.zip";
        # 备份当前版本数据库地址
        $this->backupSqlPath = root_path() . "backup/xhadmin-backup-{$this->clientVersion}.sql";

        # 备份覆盖代码地址
        $this->backCoverPath = runtime_path() . "core/xhadmin-backup-cover.zip";
    }

    /**
     * 下载更新包
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function download()
    {
        # 获取更新目标版本
        $version = (int) $this->request->get('version', 0);
        if (!$version) {
            throw new Exception('更新目标版本参数错误');
        }
        # 获取更新包下载地址
        $req = new SystemUpdateRequest;
        $req->getKey();
        $req->target_version = $version;
        $req->version        = $this->clientVersion;
        $cloud               = new Cloud($req);
        $data                = $cloud->send();
        $downUrl             = $data->url;
        # 下载更新包
        $req = new CloudRequest;
        $req->setUrl($downUrl);
        $req->setSaveFile($this->tempZipFilePath);
        $cloud = new Cloud($req);
        $cloud->send();
        # 下载成功
        return JsonMgr::successRes([
            'next' => 'backCode'
        ]);
    }

    /**
     * 备份代码
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function backCode()
    {
        try {
            # 打包至目标压缩包
            if (!is_dir(dirname($this->backupCodePath))) {
                mkdir(dirname($this->backupCodePath), 0777, true);
            }
            # 目标目录为空，直接备份走下一步
            if (DirUtil::isDirEmpty($this->targetPath)) {
                return JsonMgr::successRes([
                    'next' => 'backSql'
                ]);
            }
            # 备份原始代码
            ZipMgr::build($this->backupCodePath, $this->targetPath, $this->ignoreList);
            # 备份覆盖代码
            ZipMgr::buildFiles($this->backCoverPath, $this->targetPath, $this->backCoverList);
        } catch (\Throwable $e) {
            Log::write(
                "备份代码失败：{$e->getMessage()}，Line：{$e->getFile()}，File：{$e->getFile()}",
                'xhadmin_update_error'
            );
            throw $e;
        }
        # 打包成功
        return JsonMgr::successRes([
            'next' => 'backSql'
        ]);
    }

    /**
     * 备份数据库
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function backSql()
    {
        try {
            $config = config('database.connections.mysql');
            $mysql  = new MysqlHelper(
                $config['username'],
                $config['password'],
                $config['database'],
                $config['hostname'],
                $config['hostport'],
                $config['prefix'],
                $config['charset']
            );
            # 检测备份目录是否存在
            if (!is_dir(dirname($this->backupSqlPath))) {
                mkdir(dirname($this->backupSqlPath), 0755, true);
            }
            # 执行导出数据
            $mysql->exportSqlFile($this->backupSqlPath);
        } catch (\Throwable $e) {
            if (file_exists($this->backupSqlPath)) {
                unlink($this->backupSqlPath);
            }
            Log::write(
                "数据库备份失败：{$e->getMessage()}，Line：{$e->getFile()}，File：{$e->getFile()}",
                'xhadmin_update_error'
            );
            // return JsonMgr::fail("数据库备份失败：{$e->getMessage()}，Line：{$e->getFile()}，File：{$e->getFile()}");
        }
        return JsonMgr::successRes([
            'next' => 'unzip'
        ]);
    }

    /**
     * 解压更新包
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function unzip()
    {
        try {
            # 解压更新包
            ZipMgr::unzip($this->tempZipFilePath, $this->targetPath);
            # 解压覆盖文件
            ZipMgr::unzip($this->backCoverPath, $this->targetPath);
            # 解压成功，删除临时文件
            file_exists($this->tempZipFilePath) && unlink($this->tempZipFilePath);
            # 返回成功
            return JsonMgr::successRes([
                'next' => 'updateData'
            ]);
        } catch (\Throwable $e) {
            # 日志记录
            Log::write("框架解压出错：{$e->getMessage()}，line：{$e->getLine()}，file：{$e->getFile()}", "xhadmin_update_error");
            # 报错异常，执行回滚
            throw new RollBackException("解压出错：{$e->getMessage()}");
        }
    }

    /**
     * 执行数据同步更新
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function updateData()
    {
        try {
            # 获取更新类
            $updateDataPath = base_path() . 'common/service/UpdateDataService.php';
            if (!file_exists($updateDataPath)) {
                throw new Exception('更新类不存在');
            }
            # 重新引入更新类，确保是最新更新类
            require_once $updateDataPath;
            # 更新服务类
            $class = "app\\common\\service\\UpdateDataService";
            if (class_exists($class)) {
                $updateServiceCls = new $class($this->request, $this->clientVersion, $this->clientVersionName);
                # 执行更新前置
                $context = [];
                if (method_exists($class, 'beforeUpdate')) {
                    $context = call_user_func([$updateServiceCls, 'beforeUpdate']);
                }
                # 执行update更新
                if (method_exists($class, 'update')) {
                    call_user_func(
                        [$updateServiceCls, 'update'],
                        $context
                    );
                }
            }
        } catch (\Throwable $e) {
            # 报错异常，记录日志
            Log::write("框架更新数据出错：{$e->getMessage()}，Line：{$e->getLine()}，File：{$e->getFile()}", 'xhadmin_update_error');
            # 报错异常，执行回滚
            throw new RollBackException("数据更新数据出错：{$e->getMessage()}");
        }
        return JsonMgr::successRes([
            'next' => 'success'
        ]);
    }

    /**
     * 更新成功
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function success()
    {
        return JsonMgr::successFul('更新成功，即将跳转...', [
            'next' => ''
        ]);
    }
}
