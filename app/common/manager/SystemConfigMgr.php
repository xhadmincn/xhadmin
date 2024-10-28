<?php
namespace app\common\manager;

use app\common\builder\FormBuilder;
use app\common\enum\ConfigGroupCol;
use app\common\model\SystemConfig;
use app\common\service\UploadService;
use Exception;
use support\Request;

/**
 * 系统配置管理
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class SystemConfigMgr
{
    /**
     * 请求对象
     * @var Request
     */
    protected $request = null;
    # 扩展组件类型
    private $extraOptions = ['checkbox', 'radio', 'select'];

    /**
     * 构造函数
     * @param \support\Request $request
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 获取配置项数据
     * @param array $where
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function getConfig(array $where)
    {
        return SystemConfig::where($where)->column('value', 'name');
    }

    /**
     * 保存配置项
     * 无报错则保存成功
     * @param array $settings
     * @param array $post
     * @param mixed $store_id
     * @param mixed $saas_appid
     * @throws \Exception
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function saveData(array $settings, array $post, $store_id = null, $saas_appid = null)
    {
        $settings = array_column($settings, 'children');
        $data     = [];
        foreach ($settings as $value) {
            $data = array_merge($data, $value);
        }
        foreach ($data as $value) {
            # 保存数据
            $dataValue = empty($value['name']) ? '' : $post[$value['name']];
            # 查询数据
            $where = [
                'name'              => $value['name'],
                'store_id'          => $store_id,
                'saas_appid'        => $saas_appid,
            ];
            $model = SystemConfig::where($where)->find();
            if (!$model) {
                $model              = new SystemConfig;
                $model->name        = $value['name'];
                $model->store_id    = $store_id;
                $model->saas_appid  = $saas_appid;
            }
            // 更新数据
            $model->value = $dataValue;
            # 验证是否附件库
            if (is_array($model['value']) && $value['component'] === 'uploadify') {
                $files = [];
                foreach ($dataValue as $k => $v) {
                    $files[$k] = UploadService::path($v);
                }
                $uploadPath   = implode(',', $files);
                $model->value = $uploadPath;
            }
            if ($model->save() === false) {
                throw new Exception('保存配置失败');
            }
        }
    }

    /**
     * 获取配置项视图
     * @param array $data
     * @param mixed $store_id
     * @param mixed $saas_appid
     * @throws \Exception
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function getConfigView(array $data, $store_id = null, $saas_appid = null)
    {
        # 设置默认选中
        $first  = current($data);
        $actvie = empty($first['name']) ? '' : $first['name'];
        # 实例表单构建器
        $builder = new FormBuilder;
        $builder = $builder->setMethod('PUT');
        $builder = $builder->initTabs($actvie);
        foreach ($data as $value) {
            if (empty($value['name'])) {
                throw new Exception('分组标识不能为空');
            }
            if (empty($value['title'])) {
                throw new Exception('分组名称不能为空');
            }
            if (empty($value['children'])) {
                throw new Exception('配置项数据错误');
            }
            # 默认布局模式
            $col = 24;
            if (!empty($value['layout_col'])) {
                $colData = ConfigGroupCol::getValue($value['layout_col']);
                if (empty($colData['col'])) {
                    throw new Exception('布局模式错误');
                }
                $col = (int) $colData['col'];
            }
            $configs = $this->getConfigData($value['children'], $col, $store_id, $saas_appid);
            $builder = $builder->addTab(
                $value['name'],
                $value['title'],
                $configs
            );
        }
        $data = $builder->endTabs()->create();
        return $data;
    }
    /**
     * 获取系统配置
     * @param array $list
     * @param int $col
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    private function getConfigData(array $list, int $col, $store_id, $saas_appid): array
    {
        # 查询数据
        $configNames = array_column($list, 'name');
        $where       = [
            ['name', 'in', $configNames],
            ['store_id', '=', $store_id],
            ['saas_appid', '=', $saas_appid],
        ];
        $dataValue   = SystemConfig::where($where)->column('value', 'name');
        $config      = [];
        $builder     = new FormBuilder;
        foreach ($list as $value) {
            # 数据验证
            hpValidate(\app\admin\validate\SystemConfig::class, $value);
            # 设置数据
            $configValue = empty($dataValue[$value['name']]) ? '' : $dataValue[$value['name']];
            # 验证扩展组件
            if (in_array($value['component'], $this->extraOptions)) {
                if (empty($value['extra'])) {
                    throw new Exception("[{$value['name']}] - 扩展数据不能为空");
                }
                if (empty($value['extra']['options'])) {
                    throw new Exception("[{$value['name']}] - 扩展的选项数据不能为空");
                }
            }
            # 附件库参数设置
            if ($value['component'] == 'uploadify') {
                # 重设的模型数据
                if ($configValue) {
                    $tempConfig  = array_filter(explode(',', $configValue));
                    $configValue = UploadService::urls($tempConfig);
                } else {
                    $configValue = [];
                }
            }
            # 设置布局模式
            $value['extra']['props']['col'] = $col;
            try {
                # 普通组件
                $builder->addRow(
                    $value['name'],
                    $value['component'],
                    $value['title'],
                    $configValue,
                    $value['extra']
                );
            } catch (\Throwable $e) {
                # 自定义组件
                $builder->addComponent(
                    $value['name'],
                    $value['component'],
                    $value['title'],
                    $configValue,
                    $value['extra']
                );
            }
        }
        $config = $builder->getBuilder()->formRule();
        return $config;
    }
}