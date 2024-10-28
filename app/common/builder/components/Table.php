<?php

namespace app\common\builder\components;

use app\common\builder\ListBuilder;
use Exception;

/**
 * 表格配置
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-02
 */
trait Table
{
    // 表格唯一ID
    private $id = 'full_edit_1';
    // 表格高度
    private $height = 'auto';
    // 是否带有边框
    private $border = 'inner';
    // 是否为圆角边框
    private $round = false;
    // 表格的尺寸 medium, small, mini
    private $size = 'small';
    // 所有的列对齐方式
    private $align = 'left';
    // 所有的表头列的对齐方式
    private $headerAlign = 'left';
    // 所有的表尾列的对齐方式
    private $footerAlign = 'left';
    // 是否显示表头
    private $showHeader = true;
    // 是否显示表尾
    private $showFooter = false;
    // 设置表头所有内容过长时显示为省略号
    private $showHeaderOverflow = true;
    // 设置表尾所有内容过长时显示为省略号
    private $showOverflow = true;
    // 保持原始值的状态，被某些功能所依赖，比如编辑状态、还原数据等（开启后影响性能，具体取决于数据量）
    private $keepSource = false;
    // 行配置
    private $rowConfig = [
        'keyField' => 'id',
        'isHover'  => true,
    ];
    // 工具栏配置
    private $toolbarConfig = [
        // 是否开启刷新按钮
        'refresh' => true,
        // 是否开启导入按钮
        'import'  => false,
        // 是否开启导出按钮
        'export'  => false,
        // 是否开启打印按钮
        'print'   => false,
        // 是否开启全屏缩放按钮
        'zoom'    => true,
        // 是否开启自定义表格列
        'custom'  => true
    ];

    // 列字段名（注：属性层级越深，渲染性能就越差，例如：aa.bb.cc.dd.ee）
    private $columns = [];
    // 数据站点配置项（基于 Promise API）
    private $proxyConfig = [
        // 启用动态序号站点，每一页的序号会根据当前页数变化
        'seq'    => true,
        // 启用排序站点，当点击排序时会自动触发 query 行为
        'sort'   => true,
        // 启用筛选站点，当点击筛选时会自动触发 query 行为 
        'filter' => true,
        // 启用表单站点，当点击表单提交按钮时会自动触发 reload 行为
        'form'   => true,
        // 数据站点字段
        'props'  => [
            // 默认无分页
            'list' => 'data',
        ],
    ];
    // 是否选项表格
    private $tabsConfig = [
        'active' => '',
        'field'  => '',
        'list'   => [],
    ];

    /**
     * 设置选项卡表格
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-15
     * @param  array       $config
     * @return ListBuilder
     */
    public function tabsConfig(array $config): ListBuilder
    {
        $this->tabsConfig = array_merge($this->tabsConfig, $config);
        return $this;
    }

    /**
     * 设置选项卡表格
     * @param array $data
     * @param string $field
     * @param string $active
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function setTableTabs(array $data, string $field, string $active)
    {
        $this->tabsConfig = array_merge($this->tabsConfig, [
            'active' => $active,
            'field'  => $field,
            'list'   => $data,
        ]);
        return $this;
    }

    /**
     * 表格的表单配置
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function formConfig(array $config = []): ListBuilder
    {
        $this->formConfig = array_merge([], $config);
        return $this;
    }

    /**
     * 调整大小配置
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function resizableConfig(array $config = []): ListBuilder
    {
        $this->resizableConfig = array_merge([], $config);
        return $this;
    }

    /**
     * 菜单配置
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function menuConfig(array $config = []): ListBuilder
    {
        $this->menuConfig = array_merge([], $config);
        return $this;
    }

    /**
     * 树表格配置
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function treeConfig(array $config = []): ListBuilder
    {
        $this->treeConfig = array_merge([
            // 自动将列表转为树结构（支持虚拟滚动）
            'transform'     => true,
            // 树节点的字段名
            'rowField'      => 'id',
            // 树父节点的字段名
            'parentField'   => 'pid',
            // 树子节点的字段名
            // 'children'          => 'children',
            /**
             * 树节点的缩进
             * 需要在addColumn方法中的扩展参数内添加treeNode标注为节点
             */
            'indent'        => 20,
            // 树节点的连接线（启用连接线会降低渲染性能）
            'line'          => false,
            // 默认展开所有子孙树节点（只会在初始化时被触发一次）
            'expandAll'     => true,
            // 默认展开指定树节点（只会在初始化时被触发一次，需要有 row-config.keyField）
            'expandRowKeys' => [],
            // 对于同一级的节点，每次只能展开一个
            'accordion'     => false,
            /**
             * 触发方式（注：当多种功能重叠时，会同时触发）
             * default（点击按钮触发）, cell（点击单元格触发）, row（点击行触发）
             */
            'trigger'       => 'cell',
            // 是否使用懒加载（启用后只有指定 hasChild 的节点才允许被点击）
            'lazy'          => false,
            // 只对 lazy 启用后有效，标识是否存在子节点，从而控制是否允许被点击
            'hasChild'      => 'hasChild',
            /**
             * 是否保留展开状态
             * 对于某些场景可能会用到
             * 比如数据被刷新之后还保留之前展开的状态
             * 需要有 row-config.keyField
             */
            'reserve'       => false,
            // 是否显示图标按钮
            'showIcon'      => true,
            // 自定义展开后显示的图标
            'iconOpen'      => 'vxe-icon-square-minus',
            // 自定义收起后显示的图标
            'iconClose'     => 'vxe-icon-square-plus',
            // 自定义懒加载中显示的图标
            'iconLoaded'    => '',
        ], $config);
        return $this;
    }

    /**
     * 展开配置（不支持虚拟滚动）
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function expandConfig(array $config = []): ListBuilder
    {
        $this->expandConfig = array_merge([
            // 展开列显示的字段名，可以直接显示在单元格中
            'labelField'    => 'id',
            // 默认展开所有行（只会在初始化时被触发一次）
            'expandAll'     => true,
            // 默认展开指定行（只会在初始化时被触发一次，需要有 row-config.keyField）
            'expandRowKeys' => [],
            // 每次只能展开一行
            'accordion'     => false,
            // 展开内容的高度，默认自适应高度
            'height'        => '',
            /**
             * 触发方式（注：当多种功能重叠时，会同时触发）
             * default（点击按钮触发）, cell（点击单元格触发）, row（点击行触发）
             */
            'trigger'       => 'cell',
            // 是否使用懒加载
            'lazy'          => false,
            /**
             * 是否保留展开状态
             * 对于某些场景可能会用到
             * 比如数据被刷新之后还保留之前展开的状态
             * 需要有 row-config.keyField
             */
            'reserve'       => false,
            // 是否显示图标按钮
            'showIcon'      => true,
            // 自定义展开后显示的图标
            'iconOpen'      => 'vxe-icon-square-minus',
            // 自定义收起后显示的图标
            'iconClose'     => 'vxe-icon-square-plus',
            // 自定义懒加载中显示的图标
            'iconLoaded'    => '',
        ], $config);
        return $this;
    }

    /**
     * 提示配置
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function tooltipConfig(array $config = []): ListBuilder
    {
        $this->tooltipConfig = array_merge([], $config);
        return $this;
    }

    /**
     * 复选框配置
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function checkboxConfig(array $config = []): ListBuilder
    {
        $this->checkboxConfig = array_merge([
            'labelField' => 'id',
            'reserve'    => true,
            'highlight'  => true,
            'range'      => true
        ], $config);
        return $this;
    }

    /**
     * 单选框配置
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function radioConfig(array $config = []): ListBuilder
    {
        $this->radioConfig = array_merge([], $config);
        return $this;
    }

    /**
     * 导出配置
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function exportConfig(array $config = []): ListBuilder
    {
        // 开启导出按钮
        $this->toolbarConfig([
            'export' => true
        ]);
        // 导出配置参数
        $this->exportConfig = array_merge([
            'api'    => '',
            'remote' => true,
            'types'  => ['xlsx', 'xls'],
            'modes'  => ['current', 'selected', 'all'],
        ], $config);
        return $this;
    }

    /**
     * 导入配置
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function importConfig(array $config = []): ListBuilder
    {
        // 开启导入按钮
        $this->toolbarConfig([
            'import' => true
        ]);
        // 配置导入参数
        $this->importConfig = array_merge([
            'api'      => '',
            'isRemote' => false,
            'types'    => ['xlsx'],
            'modes'    => ['insert'],
        ], $config);
        return $this;
    }

    /**
     * 筛选查询
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function filterConfig(array $config = []): ListBuilder
    {
        $this->filterConfig = array_merge([
            'remote' => false
        ], $config);
        return $this;
    }

    /**
     * 排序配置
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function sortConfig(array $config = []): ListBuilder
    {
        $this->sortConfig = array_merge([
            'trigger' => 'cell',
            'remote'  => true
        ], $config);
        return $this;
    }

    /**
     * 行配置
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function rowConfig(array $config = []): ListBuilder
    {
        $this->rowConfig = array_merge([
            'keyField' => 'id',
            'isHover'  => true,
        ], $config);
        return $this;
    }

    /**
     * 开启编辑
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-13
     * @param  array       $config
     * @return ListBuilder
     */
    public function editConfig(array $config = []): ListBuilder
    {
        $this->editConfig = array_merge([
            'enabled'    => true,
            'trigger'    => 'dblclick',
            'mode'       => 'cell',
            'showStatus' => true
        ], $config);
        return $this;
    }

    /**
     * 打印配置
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function printConfig(array $config = []): ListBuilder
    {
        $this->printConfig = array_merge([], $config);
        return $this;
    }

    /**
     * 工具栏配置
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $toolbar
     * @return ListBuilder
     */
    public function toolbarConfig(array $toolbar = []): ListBuilder
    {
        $toolbar             = array_merge($this->toolbarConfig, $toolbar);
        $this->toolbarConfig = $toolbar;
        return $this;
    }

    /**
     * 自定义配置
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $customConfig
     * @return ListBuilder
     */
    public function customConfig(array $customConfig = []): ListBuilder
    {
        $customConfig       = array_merge([
            'storage' => true
        ], $customConfig);
        $this->customConfig = $customConfig;
        return $this;
    }

    /**
     * 列配置
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $columnConfig
     * @return ListBuilder
     */
    public function columnConfig(array $columnConfig = []): ListBuilder
    {
        $columnConfig       = array_merge([
            // 是否需要为每一列的 VNode 设置 key 属性（非特殊情况下不需要使用）
            'useKey'    => false,
            // 当鼠标点击列头时，是否要高亮当前列
            'isCurrent' => false,
            // 	当鼠标移到列头时，是否要高亮当前头
            'isHover'   => true,
            // 每一列是否启用列宽调整
            'resizable' => true,
            // 每一列的宽度 auto, px, %
            'width'     => 'auto',
            // 每一列的最小宽度 auto, px, %
            'minWidth'  => 'auto',
        ], $columnConfig);
        $this->columnConfig = $columnConfig;
        return $this;
    }

    /**
     * 添加列
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-02-27
     * @param  string      $field
     * @param  string      $title
     * @param  array       $extra
     * @return ListBuilder
     */
    public function addColumn(string $field, string $title, array $extra = []): ListBuilder
    {
        $columns         = [
            'field' => $field,
            'title' => $title
        ];
        $this->columns[] = array_merge($extra, $columns);
        return $this;
    }

    /**
     * 添加元素列
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  string      $field
     * @param  string      $title
     * @param  array       $extra
     * @return ListBuilder
     */
    public function addColumnEle(string $field, string $title, array $extra = []): ListBuilder
    {
        if (!isset($extra['params']['type'])) {
            throw new Exception('缺少元素类型');
        }
        $extra['params']['props'] = isset($extra['params']['props']) ? $extra['params']['props'] : [];
        $extra                    = array_merge([
            'slots' => [
                'default' => $extra['params']['type']
            ],
        ], $extra);
        $this->addColumn($field, $title, $extra);
        return $this;
    }

    /**
     * 添加编辑列
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-13
     * @param  string      $field
     * @param  string      $title
     * @param  array       $extra
     * @return ListBuilder
     */
    public function addColumnEdit(string $field, string $title, array $extra = []): ListBuilder
    {
        $extra = array_merge_recursive([
            'editRender' => [
                'name'  => 'input',
                'attrs' => [
                    'placeholder' => "请输入{$title}",
                ],
            ],
        ], $extra);
        $this->addColumn($field, $title, $extra);
        return $this;
    }

    /**
     * 分页配置
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $pagerConfig
     * @param  array       $field
     * @return ListBuilder
     */
    public function pageConfig(array $pagerConfig = [], array $field = []): ListBuilder
    {
        // 当前页码
        $currentPage = (int) request()->get('page', 1);
        // 分页配置
        $config = config('paginator', []);
        // 每页数量
        $listRows                   = isset($config['listRows']) ? $config['listRows'] : 20;
        $this->pagerConfig          = array_merge([
            'currentPage' => $currentPage,
            'pageSize'    => $listRows,
            'total'       => 1000,
            'pageSizes'   => [10, 15, 20, 50, 100, 200, 500, 1000],
            'align'       => 'right',
            'background'  => false,
            'perfect'     => false,
            'border'      => false,
            'layouts'     => [
                'PrevJump',
                'PrevPage',
                'Number',
                'NextPage',
                'NextJump',
                'Sizes',
                'FullJump',
                'Total'
            ]
        ], $pagerConfig);
        $this->proxyConfig['props'] = array_merge([
            'result' => 'data.data',
            'total'  => 'data.total',
        ], $field);
        return $this;
    }

    /**
     * 单项表格配置
     *
     * @Author 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  string       $key
     * @param  mixed        $value
     * @return ListBuilder
     */
    public function config(string $key, $value): ListBuilder
    {
        $this->$key = $value;
        return $this;
    }
}
