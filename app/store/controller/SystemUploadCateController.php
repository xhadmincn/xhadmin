<?php

namespace app\store\controller;

use app\common\trait\UploadCateTrait;
use app\common\BaseController;

/**
 * 附件分类管理器
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-04
 */
class SystemUploadCateController extends BaseController
{
    # 附件库分类
    use UploadCateTrait;

    /**
     * 站点ID
     * @var int|null
     */
    protected $store_id = null;

    /**
     * 构造函数
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function initialize()
    {
        parent::initialize();
        $this->store_id = $this->request->user['id'];
    }
}
