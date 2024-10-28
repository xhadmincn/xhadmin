<?php
namespace app\common\utils;

/**
 * 自定义分页类
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-04-23
 */
class Paginator extends \think\Paginator
{
    /**
     * 构造函数
     * @param mixed $items
     * @param int $listRows
     * @param int $currentPage
     * @param int|null $total
     * @param bool $simple
     * @param array $options
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-23
     */
    public function __construct($items, int $listRows, int $currentPage = 1, int $total = null, bool $simple = false, array $options = [])
    {
        // 分页配置
        $config = config('paginator');
        // 每页数量
        $listRows = isset($config['listRows']) ? $config['listRows'] : 20;
        // 精简分页模式
        $simple = isset($config['simple']) ? $config['simple'] : false;
        // 参数选项
        $options = isset($config['options']) ? $config['options'] : [];
        // 继承父级分页类
        parent::__construct($items, $listRows, $currentPage, $total, $simple, $options);
    }

    /**
     * 获取每页数量.
     *
     * @return int
     */
    public function listRows(): int
    {
        $get            = request()->get();
        $this->listRows = isset($get['limit']) ? (int)$get['limit'] : $this->listRows;
        return $this->listRows;
    }

    /**
     * 渲染分页html.
     * @return mixed
     */
    public function render()
    {
    }
}