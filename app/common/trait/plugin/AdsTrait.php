<?php
namespace app\common\trait\plugin;

use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\common\model\plugin\PluginAds;
use app\common\utils\Json;
use Exception;
use support\Request;
use think\App;

/**
 * 广告系统
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
trait AdsTrait
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
     * @var PluginAds
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
        $this->model = new PluginAds;
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
        $active = $request->get('position','');
        $position = config('plugin.'.$request->plugin.'.advertisement') ?? [];
        if (empty($active) && $position) {
            $active = current($position)['value'];
        }
        $builder = new ListBuilder;
        $data = $builder
            ->addActionOptions('操作', [
                'width'         => 180
            ])
            ->pageConfig()
            ->tabsConfig([
                'active'        => $active,
                'field'         => 'position',
                'list'          => $position
            ])
            ->addTopButton('add', '添加', [
                'api'           => $this->pluginPrefix.'/admin/Ads/add',
                'path'          => '/Ads/add',
            ], [], [
                'type'          => 'primary'
            ])
            ->addRightButton('edit', '修改', [
                'api'           => $this->pluginPrefix.'/admin/Ads/edit',
                'path'          => '/Ads/edit',
            ], [], [
                'type'          => 'primary',
            ])
            ->addRightButton('del', '删除', [
                'type'          => 'confirm',
                'api'           => $this->pluginPrefix.'/admin/Ads/del',
                'method'        => 'delete',
            ], [
                'type'          => 'error',
                'title'         => '温馨提示',
                'content'       => '是否确认删除该数据',
            ], [
                'type'          => 'danger',
            ])
            ->addColumn('create_at', '创建时间',[
                'width'         => 150
            ])
            ->addColumn('title', '广告名称')
            ->addColumnEle('image_url', '广告图片', [
                'width' => 120,
                'params' => [
                    'type' => 'image',
                ],
            ])
            ->addColumnEle('status', '状态', [
                'width' => 100,
                'params' => [
                    'type' => 'switch',
                    'api' => $this->pluginPrefix.'/admin/Ads/rowEdit',
                    'unchecked' => [
                        'text' => '禁用',
                        'value' => '10'
                    ],
                    'checked' => [
                        'text' => '正常',
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
        $data = $this->model->paginate();
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
            if (empty($post['name'])) {
                return $this->fail('请输入标签名称');
            }
            $position = config('plugin.'.$request->plugin.'.advertisement') ?? [];
            # 查找位置
            $positionIndex = array_search($post['name'], array_column($position, 'value'));
            $positionData = $position[$positionIndex] ?? [];
            if (empty($positionData)) {
                return $this->fail('广告位置不存在');
            }
            $post['name'] = $positionData['value'] ?? '';
            $post['category'] = $positionData['label'] ?? '';

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
        $id    = $request->get('id','');
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
            if (empty($post['name'])) {
                return $this->fail('请输入标签名称');
            }
            
            $position = config('plugin.'.$request->plugin.'.advertisement') ?? [];
            # 查找位置
            $positionIndex = array_search($post['name'], array_column($position, 'value'));
            $positionData = $position[$positionIndex] ?? [];
            if (empty($positionData)) {
                return $this->fail('广告位置不存在');
            }
            $post['name'] = $positionData['value'] ?? '';
            $post['category'] = $positionData['label'] ?? '';

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
        $id = $request->post('id','');
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
        $request = request();
        $options = config('plugin.'.$request->plugin.'.advertisement') ?? [];
        $builder = new FormBuilder;
        $data    = $builder
            ->addRow('title', 'input', '广告名称', '', [
                'col' => 12,
            ])
            ->addRow('name', 'select', '广告位置', '', [
                'col'       => 12,
                'options'   => $options
            ])
            ->addComponent('image_url', 'uploadify', '广告图片', '', [
                'col'       => 12,
            ])
            ->addRow('link_url', 'input', '跳转连接（可选）', '', [
                'col' => 12,
            ]);
        return $data;
    }
}