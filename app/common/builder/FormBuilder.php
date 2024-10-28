<?php

namespace app\common\builder;

use app\common\Model;
use FormBuilder\Driver\CustomComponent;
use FormBuilder\Factory\Elm;
use FormBuilder\Form;
use think\Validate;

/**
 * @title 表单构造器
 * @desc 用于表单UI快速生成
 * @author 楚羽幽 <admin@hangpu.net>
 */
class FormBuilder extends Form
{
    // Tabs表单构造器
    use TabsFormBuilder;
    
    /**
     * 表单对象
     * @var Form
     */
    private $builder;

    // 选项卡对象
    private $tabBuilder;
    // 表单数据规则
    private $data;
    // 请求对象
    private $request;

    // 额外扩展配置
    protected $extraConfig = [];

    // 提交后重定向地址
    protected $redirect = '';

    /**
     * 构造函数
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function __construct()
    {
        $this->request = request();
        $url           = $this->request->baseUrl();
        $rule          = [];
        $config        = [];
        $this->builder = Form::elm($url, $rule, $config);

        // 额外扩展配置
        $extraConfig       = [
            'submitBtn' => [
                'type' => 'primary',
                'content' => '提交保存',
                'show' => true,
                'style' => [
                    'padding' => '0 30px'
                ]
            ],
            'resetBtn' => [
                'type' => 'info',
                'content' => '数据重置',
                'show' => true,
                'style' => [
                    'padding' => '0 30px'
                ]
            ]
        ];
        $this->extraConfig = $extraConfig;
    }

    /**
     * 表单配置
     * @param string $name
     * @param array $value
     * @return FormBuilder
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function setConf(string $name, array $value): FormBuilder
    {
        $this->extraConfig[$name] = $value;
        return $this;
    }

    /**
     * 添加表单行元素
     * @param string $field
     * @param string $type
     * @param string $title
     * @param mixed $value
     * @param array $extra
     * @return FormBuilder
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-05
     */
    public function addRow(string $field, string $type, string $title, $value = '', array $extra = []): FormBuilder
    {
        # 创建组件
        $component = Elm::$type($field, $title, $value);
        # 设置字段，默认数据等
        $component->field($field)->title($title)->value($value);
        # 设置后缀提示语
        if (isset($extra['prompt'])) {
            $promptData = $extra['prompt'];
            unset($extra['prompt']);
            $prompt['type'] = 'prompt-tip';
            # 设置默认提示语
            if (is_array($promptData) && isset($promptData['text'])) {
                $prompt['props'] = $promptData;
                unset($prompt['text']);
            }
            # 设置字符提示语
            if (is_string($promptData) && !empty($promptData)) {
                $prompt['props']['text'] = $promptData;
            }
            # 插入组件
            $component->appendRule('suffix',$prompt);
        }
        # 设置组件属性
        foreach ($extra as $componentType => $componentValue) {
            $component->$componentType($componentValue);
        }
        # 设置组件
        $this->builder->append($component);
        # 返回组件
        return $this;
    }

    /**
     * 添加自定义组件
     * @param string $field
     * @param string $type
     * @param string $title
     * @param mixed $value
     * @param array $extra
     * @return FormBuilder
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-05
     */
    public function addComponent(string $field, string $type, string $title, $value = '', array $extra = []): FormBuilder
    {
        # 创建自定义组件
        $component = new CustomComponent($type);
        # 设置字段，默认数据等
        $component->field($field)->title($title)->value($value);
        # 设置组件属性
        foreach ($extra as $componentType => $componentValue) {
            $component->$componentType($componentValue);
        }
        # 设置组件
        $this->builder->append($component);
        # 返回组件
        return $this;
    }

    /**
     * 创建表单分割线
     * @param string $title
     * @param array $extra
     * @return FormBuilder
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function addDivider(string $title, array $extra = []): FormBuilder
    {
        # 创建自定义组件
        $component = new CustomComponent('el-divider');
        # 设置标题
        $component->appendChild($title);
        # 设置组件属性
        $component->props($extra);
        $this->builder->append($component);
        return $this;
    }


    /**
     * 表单验证
     * @param mixed $validate
     * @return FormBuilder
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function formValidate($validate): FormBuilder
    {
        /**
         * 实例验证类
         * @var Validate
         */
        $class = new $validate;
        return $this;
    }


    /**
     * 设置表单渲染数据
     * @param array $data
     * @return FormBuilder
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function setFormData(array $data): FormBuilder
    {
        $this->builder->formData($data);
        return $this;
    }

    /**
     * 设置请求方式
     * @param mixed $method
     * @return FormBuilder
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function setMethod($method = 'GET'): FormBuilder
    {
        $this->builder->setMethod(strtoupper($method));
        return $this;
    }

    /**
     * 设置请求地址
     * @param mixed $action
     * @return FormBuilder
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function setAction($action = ''): FormBuilder
    {
        $this->builder->setAction($action);
        return $this;
    }

    /**
     * 设置提交后重定向地址
     * @param string $url
     * @return FormBuilder
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function setRedirect(string $url)
    {
        $this->redirect = $url;
        return $this;
    }

    /**
     * 设置数据
     * @param Model $model
     * @return FormBuilder
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function setData(Model $model): FormBuilder
    {
        if (!is_array($model)) {
            $data = $model->toArray();
            $this->builder->formData($data);
        }
        return $this;
    }

    /**
     * 快速生成表单
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function create(): array
    {
        $apiUrl                       = $this->builder->getAction();
        $method                       = $this->builder->getMethod();
        $this->data['http']['api']    = $apiUrl;
        $this->data['http']['method'] = $method;
        $this->data['config']         = $this->builder->formConfig();
        $this->data['extraConfig']    = $this->extraConfig;
        $this->data['formRule']       = $this->builder->formRule();
        $this->data['redirect']       = $this->redirect;
        return $this->data;
    }

    /**
     * 获取builder生成类对象
     * @return Form
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function getBuilder(): Form
    {
        return $this->builder;
    }
}