<?php
namespace app\common\trait\config;

use app\common\manager\SettingsMgr;
use app\common\model\SystemConfig;
use app\common\service\UploadService;
use Exception;
use app\common\utils\Json;
use support\Request;

/**
 * 选项卡配置
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
trait TabsConfig
{
    # 使用JSON工具类
    use Json;
    # 使用表单工具类
    use FormUtil;

    /**
     * 应用ID（null则获取系统配置）
     * @var string|null
     */
    protected $saas_appid = null;

    /**
     * 获取选项卡配置项
     * @param \support\Request $request
     * @return mixed
     * @author John
     */
    public function tabs(Request $request)
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
            foreach ($configTemplate as $item) {
                foreach ($item['children'] as $children) {
                    # 处理附件库数据
                    if ($children['component'] === 'uploadify') {
                        $post[$children['name']] = UploadService::path($post[$children['name']]);
                    }
                }
            }
            # 重构数据
            $data = [
                'active'        => $active,
                'children'      => $post
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
        $formData = empty($configData['children']) ? [] : $configData['children'];
        foreach ($configTemplate as $item) {
            foreach ($item['children'] as $value) {
                # 处理附件库数据
                if (isset($formData[$value['name']]) && $value['component'] === 'uploadify') {
                    $formData[$value['name']] = UploadService::url($formData[$value['name']]);
                }
            }
        }
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
        $view = $this->getTabsView($active, $configTemplate)
        ->setMethod('PUT')
        ->setFormData($formData)
        ->create();
        return $this->successRes($view);
    }
    
    
    /**
     * 获取无选中Tabs选项卡
     * @param \support\Request $request
     * @throws \Exception
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function config(Request $request)
    {
        $group  = $request->get('group','');
        if (empty($group)) {
            throw new Exception('缺少配置分组参数');
        }
        $plugin = $request->plugin;
        if (empty($plugin) && empty($this->saas_appid)) {
            $config = config('settings');
        } else {
            $config = config('plugin.' . $plugin . '.settings');
        }
        if (empty($config)) {
            throw new Exception('找不到Tabs配置文件');
        }
        # 获取实际模板数据
        $configTemplate = isset($config[$group]) ? $config[$group] : [];
        if (empty($configTemplate)) {
            throw new Exception('找不到标准的配置文件');
        }
        # 保存数据
        if ($request->isPut()) {
            $post   = $request->post();
            if (empty($post['active'])) {
                throw new Exception('请选择选项卡');
            }
            unset($post['active']);
            # 查询数据
            foreach ($configTemplate as $item) {
                $where = [
                    'group'         => $item['name'],
                    'saas_appid'    => $this->saas_appid,
                ];
                $model = SystemConfig::where($where)->find();
                if (!$model) {
                    $model             = new SystemConfig;
                    $model->group      = $item['name'];
                    $model->saas_appid = $this->saas_appid;
                }
                # 处理数据
                $configValue = [];
                foreach ($item['children'] as $children) {
                    $configValue[$children['name']] = $post[$children['name']];
                    # 处理附件库数据
                    if ($children['component'] === 'uploadify') {
                        $configValue[$children['name']] = UploadService::path($post[$children['name']]);
                    }
                }
                # 设置保存数据
                $model->value = $configValue;
                # 保存数据
                if (!$model->save()) {
                    throw new Exception("保存分组[{$item['title']}]失败");
                }
            }
            # 保存成功
            return $this->success('保存成功');
        }
        # 设置默认选中
        $first  = current($configTemplate);
        $active = empty($first['name']) ? '' : $first['name'];
        # 获取数据
        $where      = [
            'saas_appid' => $this->saas_appid,
        ];
        $configData = getHpConfig('', $this->saas_appid);
        # 处理数据
        $formData = empty($configData) ? [] : $configData;
        foreach ($configTemplate as $item) {
            foreach ($item['children'] as $value) {
                # 处理附件库数据
                if (isset($formData[$value['name']]) && $value['component'] === 'uploadify') {
                    $formData[$value['name']] = UploadService::url($formData[$value['name']]);
                }
            }
        }
        # 获取渲染视图
        $view = $this->getTabsView($active, $configTemplate)
            ->setMethod('PUT')
            ->setFormData($formData)
            ->create();
        return $this->successRes($view);
    }
}