<?php

namespace app\store\controller;

use app\common\trait\UploadTrait;
use app\common\BaseController;

/**
 * 附件管理
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class SystemUploadController extends BaseController
{
    # 附件库
    use UploadTrait;

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
