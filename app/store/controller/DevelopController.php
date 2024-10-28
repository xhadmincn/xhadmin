<?php

namespace app\store\controller;

use app\common\BaseController;
use app\common\builder\FormBuilder;
use app\common\enum\PlatformTypes;
use app\common\enum\UploadifyAuthEnum;
use app\common\enum\YesNoEum;
use app\common\manager\StoreMgr;
use app\common\utils\DirUtil;
use app\store\model\StoreApp;
use app\store\service\develop\CopyFiles;
use app\store\service\develop\CreateProject;
use app\store\service\develop\DataCheck;
use app\store\service\develop\PluginInstall;
use app\store\validate\Develop;
use Exception;
use support\Request;
use think\facade\Db;

/**
 * 开发者项目
 * @author 贵州猿创科技有限公司
 * @copyright (c) 2023
 */
class DevelopController extends BaseController
{
    # 复制文件
    use CopyFiles;
    # 菜单数据处理
    use DataCheck;
    # 应用插件安装
    use PluginInstall;
    # 创建项目
    use CreateProject;
    
    /**
     * 模型
     * @var StoreApp
     */
    public $model;

    /**
     * 插件模板路径
     * @var string
     */
    protected $pluginTplPath = null;

    /**
     * 插件目标路径
     * @var string|null
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $pluginPath = null;

    /**
     * 插件标识（带团队标识）
     * @var string|null
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $teamPluginName = null;

    /**
     * 插件标识（不带团队标识，开头大写）
     * @var string|null
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $pluginName = null;

    /**
     * 插件标识（不带团队标识，开头小写）
     * @var string|null
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $pluginComplete = null;

    /**
     * 项目名称
     * @var string|null
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $projectName = null;

    /**
     * 数据库配置
     * @var array|null
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $_mysql = null;

    /**
     * 构造函数
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public function initialize()
    {
        parent::initialize();
        $this->model         = new StoreApp;
        $this->pluginTplPath = base_path() . 'common/template/plugin/';
        $_config = config('database');
        if (empty($_config['default']) || empty($_config['connections'])) {
            throw new Exception("数据库链接配置错误");
        }
        $_mysql = $_config['connections'][$_config['default']];
        if (empty($_mysql)) {
            throw new Exception($_config['default'] . "链接配置错误");
        }
        $this->_mysql = $_mysql;
    }

    /**
     * 创建开发者应用
     * @param \support\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function create(Request $request)
    {
        if ($request->method() === 'POST') {
            $post             = $request->post();
            $store            = $request->user;
            $post['store_id'] = $store['id'];
            $post['status']   = '20';

            hpValidate(Develop::class, $post);

            # 检查应用是否存在
            $teamName   = $post['team'];
            $appName    = ucfirst($post['name']);
            $pluginName = "{$teamName}{$appName}";
            $pluginPath = root_path() . 'plugin/' . $pluginName;
            if (is_dir($pluginPath)) {
                return $this->fail("该应用【{$pluginName}】已存在");
            }
            # 应用完整目录
            $this->pluginPath       = $pluginPath;
            # 团队名称+应用插件名称
            $this->teamPluginName   = $pluginName;
            # 应用插件名称（开头大写）
            $this->pluginName       = $appName;
            # 应用插件名称（开头小写）
            $this->pluginComplete   = $post['name'];
            # 项目名称
            $this->projectName      = $post['title'];

            Db::startTrans();
            try {
                # 复制应用文件
                $this->copyTplFile($post);
                # 处理菜单数据
                $this->checkMenuData($post);
                # 安装应用
                $this->pluginInstall($post);
                # 创建项目
                $this->createProject($post);
                Db::commit();
            } catch (\Throwable $e) {
                Db::rollback();
                # 安装失败，删除插件目录
                if (is_dir($this->pluginPath)) {
                    DirUtil::delDir($this->pluginPath);
                }
                throw $e;
            }
            return $this->success('操作成功');
        }
        $data = $this->formView()->create();
        return $this->successRes($data);
    }


    /**
     * 表单视图
     * @return \app\common\builder\FormBuilder
     * @author John
     */
    protected function formView(): FormBuilder
    {
        $store_id = request()->user['id'];
        $store = StoreMgr::detail(['id'=> $store_id]);
        $builder = new FormBuilder;
        $builder->setMethod('POST');
        $builder->addRow('title', 'input', '项目名称', '', [
            'col' => 12,
        ]);
        $builder->addRow('platform', 'select', '项目类型', '', [
            'col'       => 12,
            'multiple'  => true,
            'options'   => PlatformTypes::getOptions()
        ]);
        $builder->addRow('team', 'input', '团队标识', '', [
            'col' => 12,
        ]);
        $builder->addRow('name', 'input', '应用标识', '', [
            'col' => 12,
        ]);
        $builder->addRow('username', 'input', '超管账号', '', [
            'col' => 12,
        ]);
        $builder->addRow('password', 'input', '登录密码', '', [
            'col' => 12,
        ]);
        $builder->addRow('is_system', 'radio', '基本配置', '20', [
            'col'       => 12,
            'options'   => [
                [
                    'label' => '必须',
                    'disabled' => true,
                    'value' => '20',
                ],
            ]
        ]);
        $builder->addRow('is_auth', 'radio', '权限管理', '20', [
            'col' => 12,
            'options' => [
                [
                    'label' => '必须',
                    'disabled' => true,
                    'value' => '20',
                ],
            ]
        ]);
        $builder->addRow('is_uploadify', 'radio', '附件库权限', $store['is_uploadify'], [
            'col' => 12,
            'options' => UploadifyAuthEnum::getOptions(true)
        ]);
        $builder->addRow('is_image', 'radio', '广告管理', '20', [
            'col' => 12,
            'options' => [
                [
                    'label' => '必须',
                    'disabled' => true,
                    'value' => '20',
                ],
            ]
        ]);
        $builder->addRow('is_page', 'radio', '单页管理', '20', [
            'col' => 12,
            'options' => YesNoEum::getOptions()
        ]);
        $builder->addRow('is_article', 'radio', '文章系统', '20', [
            'col' => 12,
            'options' => YesNoEum::getOptions()
        ]);
        $builder->addRow('is_pay', 'radio', '支付配置', '10', [
            'col' => 12,
            'options' => YesNoEum::getOptions()
        ]);
        $builder->addRow('is_ad', 'radio', '流量主配置', '10', [
            'col' => 12,
            'options' => YesNoEum::getOptions()
        ]);
        $builder->addRow('is_sms', 'radio', '短信配置', '10', [
            'col' => 12,
            'options' => YesNoEum::getOptions()
        ]);
        $builder->addComponent('logo', 'uploadify', '项目图标', '', [
            'col' => 12,
            'props' => [
                'type' => 'image',
                'format' => ['jpg', 'jpeg', 'png']
            ],
        ]);
        return $builder;
    }
}