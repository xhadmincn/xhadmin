<?php

namespace app\common\trait\plugin;

use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\common\enum\StatusEnum;
use app\common\model\plugin\PluginArticles;
use app\common\utils\Json;
use Exception;
use support\Request;
use think\App;

/**
 * 文章系统
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
trait AriclesTrait
{
    // 使用JSON工具类
    use Json;

    /**
     * 应用ID（null则获取系统配置）
     * @var int|null
     */
    protected $saas_appid = null;

    /**
     * 模型
     * @var PluginArticles
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $model = null;

    /**
     * 构造函数
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new PluginArticles;
    }

    /**
     * 模型
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function indexGetTable(Request $request)
    {
        $builder = new ListBuilder;
        $data = $builder
            ->addActionOptions('操作', [
                'width'         => 180
            ])
            ->pageConfig()
            ->addTopButton('add', '添加', [
                'api'           => $this->pluginPrefix . '/admin/Articles/add',
                'path'          => '/Articles/add',
            ], [], [
                'type'          => 'primary'
            ])
            ->addRightButton('edit', '修改', [
                'api'           => $this->pluginPrefix . '/admin/Articles/edit',
                'path'          => '/Articles/edit',
            ], [], [
                'type'          => 'primary',
            ])
            ->addRightButton('del', '删除', [
                'type'          => 'confirm',
                'api'           => $this->pluginPrefix . '/admin/Articles/del',
                'method'        => 'delete',
            ], [
                'type'          => 'error',
                'title'         => '温馨提示',
                'content'       => '是否确认删除该数据',
            ], [
                'type'          => 'danger',
            ])
            ->addColumn('create_at', '创建时间', [
                'width'         => 150
            ])
            ->addColumn('category.title', '文章分类', [
                'width'         => 150
            ])
            ->addColumnEle('thumb', '封面', [
                'width' => 60,
                'params' => [
                    'type' => 'image',
                ],
            ])
            ->addColumn('title', '标题名称')
            ->addColumnEle('link', 'H5链接', [
                'params'        => [
                    'type'      => 'link',
                    'props'     => [
                        'copy'  => true,
                        'text'  => '点击打开'
                    ]
                ]
            ])
            ->addColumn('view', '文章热度', [
                'width'         => 100
            ])
            ->addColumnEle('status', '状态', [
                'width' => 150,
                'params' => [
                    'type' => 'switch',
                    'api' => $this->pluginPrefix . '/admin/Articles/rowEdit',
                    'unchecked' => [
                        'text' => '未发布',
                        'value' => '10'
                    ],
                    'checked' => [
                        'text' => '已发布',
                        'value' => '20'
                    ],
                ],
            ])
            ->create();
        return $this->successRes($data);
    }

    /**
     * 列表
     * @param \support\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function index(Request $request)
    {
        $orderBy = 'view desc,id desc';
        $data = $this->model->with(['category'])->order($orderBy)->paginate();
        return $this->successRes($data);
    }

    /**
     * 添加
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function add(Request $request)
    {
        if ($request->method() == 'POST') {
            $post = $request->post();

            # 数据验证
            if (empty($post['title'])) {
                return $this->fail('请输入标题名称');
            }
            if (empty($post['cid'])) {
                return $this->fail('请选择文章分类');
            }

            # 验证是否已存在
            $where = [
                'title'      => $post['title']
            ];
            if ($this->model->where($where)->count()) {
                return $this->fail('该文章已发布');
            }

            $model = $this->model;
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $data = $this->getFormView()->setMethod('POST')->create();
        return $this->successRes($data);
    }
    /**
     * 修改
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function edit(Request $request)
    {
        $id    = $request->get('id', '');
        $where = [
            'id'        => $id
        ];
        $model = $this->model->where($where)->find();
        if (!$model) {
            throw new Exception('数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();

            # 数据验证
            if (empty($post['title'])) {
                return $this->fail('请输入标题名称');
            }
            if (empty($post['cid'])) {
                return $this->fail('请选择文章分类');
            }

            # 验证是否已存在
            $where = [
                ['id', '<>', $id],
                ['title', '=', $post['title']]
            ];
            if ($this->model->where($where)->count()) {
                return $this->fail('该文章已发布');
            }

            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $data = $this->getFormView()->setMethod('PUT')->setData($model)->create();
        return $this->successRes($data);
    }

    /**
     * 删除
     * @param \support\Request $request
     * @throws \Exception
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function del(Request $request)
    {
        $id = $request->post('id', '');
        $where = [
            'id'        => $id
        ];
        $model = $this->model->where($where)->find();
        if (!$model) {
            throw new Exception('数据不存在');
        }
        if (!$model->delete()) {
            return $this->fail('删除失败');
        }
        return $this->success('删除成功');
    }

    /**
     * 获取表单视图
     * @return FormBuilder
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private function getFormView()
    {
        $builder = new FormBuilder;
        $data    = $builder
            ->addRow('title', 'input', '标题名称', '', [
                'col' => 12,
            ])
            ->addRow('cid', 'select', '所属分类', '', [
                'col'       => 12,
                'options'   => $this->model->getCategoryOptions()
            ])
            ->addRow('status', 'radio', '文章状态', '20', [
                'col'       => 12,
                'options'   => StatusEnum::getOptions()
            ])
            ->addRow('view', 'input', '文章热度', '0', [
                'col'       => 12,
            ])
            ->addComponent('thumb', 'uploadify', '文章封面', '', [
                'col'       => 12,
            ])
            ->addRow('desc', 'textarea', '简短描述', '', [
                'col'       => 12,
            ])
            ->addComponent('content', 'wangEditor', '文章内容', '', []);
        return $data;
    }
}
