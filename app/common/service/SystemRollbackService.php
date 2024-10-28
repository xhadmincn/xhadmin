<?php
namespace app\common\service;

use app\common\manager\JsonMgr;
use app\common\manager\ZipMgr;
use app\common\utils\DirUtil;
use support\Request;

/**
 * 框架更新回滚服务类
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class SystemRollbackService extends SystemUpdateService
{
    /**
     * 构造函数
     * @param \support\Request $request
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    /**
     * 开始进行回滚
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function startRollback()
    {
        # 删除代码文件
        // DirUtil::delDir($this->targetPath);
        # 解压备份压缩包
        ZipMgr::unzip($this->backupCodePath, $this->targetPath);
        # 解压覆盖压缩包
        ZipMgr::unzip($this->backCoverPath, $this->targetPath);
        # 删除更新SQL目录
        $updateDir = root_path().'/update';
        if (is_dir($updateDir)) {
            DirUtil::delDir($updateDir);
        }
        # 回滚完成
        return JsonMgr::fail('更新失败，已回滚至上个版本');
    }
}