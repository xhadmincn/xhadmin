<?php
namespace app\common\trait\config;

use app\common\manager\SettingsMgr;
use app\common\model\SystemConfig;
use app\common\service\UploadService;
use Exception;
use app\common\utils\Json;
use support\Request;

/**
 * 组件式配置项
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
trait ComponentConfig
{
    # 使用JSON工具类
    use Json;
    # 获取表单视图
    use FormUtil;

    /**
     * 应用ID（null则获取系统配置）
     * @var string|null
     */
    protected $saas_appid = null;

    /**
     * 扩展组件类型（即将废除）
     * @var array
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $extraOptions = ['checkbox', 'radio', 'select'];

    /**
     * 附件库配置
     * @param \support\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function component(Request $request)
    {
        $group  = $request->get('group','');
        $plugin = $request->plugin;
        if (empty($plugin) && empty($this->saas_appid)) {
            $config = config('tabsconfig');
        } else {
            $config = config('plugin.' . $plugin . '.tabsconfig');
        }
        if (empty($config)) {
            throw new Exception('找不到Tabs配置文件');
        }
        if (empty($config[$group])) {
            throw new Exception('找不到标准的配置文件');
        }
        # 获取模板数据
        $configTemplate = $config[$group];
        $template = $configTemplate['extra']['control'] ?? [];
        if (empty($template)) {
            throw new Exception('模板数据错误');
        }
        # 保存数据
        if ($request->isPut()) {
            $post = $request->post();
            $active = $post['active'];
            if (empty($active)) {
                throw new Exception('请选择选项卡');
            }
            unset($post['active']);
            # 查询数据
            $where = [
                'group'         => $group,
                'saas_appid'    => $this->saas_appid,
            ];
            $model = SystemConfig::where($where)->find();
            if (!$model) {
                $model              = new SystemConfig;
                $model->group       = $group;
                $model->saas_appid  = $this->saas_appid;
            }
            foreach ($template as $item) {
                foreach ($item['rule'] as $children) {
                    # 处理附件库数据
                    if ($children['component'] === 'uploadify') {
                        $post[$children['name']] = UploadService::path($post[$children['name']]);
                    }
                }
            }
            $configValue = SettingsMgr::getChildren($this->saas_appid, $group, []);
            # 重组储存数据
            $configData = [
                $active     => $post
            ];
            $data = [
                'active'        => $active,
                'children'      => array_merge($configValue,$configData),
            ];
            # 设置保存数据
            $model->value = $data;
            # 保存数据
            if (!$model->save()) {
                throw new Exception("保存分组[{$item['title']}]失败");
            }
            # 保存成功
            return $this->success('保存成功');
        }
        # 默认选中
        $active = '';
        # 获取数据
        $where      = [
            'group'         => $group,
            'saas_appid'    => $this->saas_appid,
        ];
        $configData = SettingsMgr::getOriginConfig($where,[]);
        $children   = empty($configData['children']) ? [] : $configData['children'];
        foreach ($template as $key=>$item) {
            foreach ($item['rule'] as $chiKey=>$value) {
                # 设置默认数据
                $template[$key]['rule'][$chiKey]['value'] = empty($children[$item['value']][$value['name']]) ? '' : $children[$item['value']][$value['name']];
                # 处理附件库数据
                if (isset($children[$value['name']]) && $value['component'] === 'uploadify') {
                    $template[$key]['rule'][$chiKey]['value'] = UploadService::url($children[$item['value']][$value['name']]);
                }
            }
        }
        $configTemplate['extra']['control'] = $template;
        # 优先使用数据库配置
        if (isset($configData['active'])) {
            $active = $configData['active'];
        }
        # 使用默认选中项
        if (empty($active)) {
            $first  = current($configTemplate);
            $active = empty($first['name']) ? '' : $first['name'];
        }
        # 获取渲染视图
        $view = $this->getComponentView($active,$configTemplate)
        ->setMethod('PUT')
        ->create();
        return $this->successRes($view);
    }
}