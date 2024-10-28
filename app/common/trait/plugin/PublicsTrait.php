<?php

namespace app\common\trait\plugin;

use app\common\manager\PluginMgr;
use app\common\manager\SettingsMgr;
use app\common\manager\StoreAppMgr;
use app\common\model\plugin\PluginAdmin;
use app\common\service\SystemInfoService;
use app\common\service\UploadService;
use app\common\utils\Json;
use app\common\utils\Password;
use Exception;
use support\Request;
use think\App;
use think\facade\Session;
use think\helper\Str;
use app\admin\validate\SystemAdmin as SystemAdminValidate;

/**
 * 应用公共管理
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
trait PublicsTrait
{
    // 使用JSON工具类
    use Json;

    /**
     * 不需要登录的方法
     * @var string[]
     */
    protected $noNeedLogin = ['site', 'login'];

    /**
     * 应用ID（null则获取系统配置）
     * @var int|null
     */
    protected $saas_appid = null;

    /**
     * 模型
     * @var PluginAdmin
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $model = null;

    /**
     * 合并外部数据
     * @var array
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $appendData = [];

    /**
     * 构造函数
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new PluginAdmin;
    }

    /**
     * 应用入口
     * @param \support\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function site(Request $request)
    {
        $pluginVersion      = PluginMgr::getPluginVersionData($request->plugin);
        $systemInfo         = SystemInfoService::info();
        $project            = SettingsMgr::group($request->appid, 'system');
        if (!empty($project['web_logo'])) {
            $project['web_logo'] = UploadService::url($project['web_logo']);
        }
        if (empty($project['web_name'])) {
            $storeApp = StoreAppMgr::detail(['id' => $request->appid]);
            $project  = [
                'web_name'      => $storeApp['title'],
                'web_title'     => '管理员登录',
                'web_logo'      => $storeApp['logo']
            ];
        }
        $pluginPrefix       = "app/{$request->plugin}";
        $data = [
            "web_name"                  => $project['web_name'],
            "web_title"                 => $project['web_title'] ?? "管理员登录",
            "web_logo"                  => $project['web_logo'],
            "appid"                     => $request->appid,
            'version_name'              => $pluginVersion['version_name'],
            'version'                   => $pluginVersion['version'],
            # 版权token
            'empower_token'             => $systemInfo['site_encrypt'],
            # 版权私钥
            'empower_private_key'       => $systemInfo['privatekey'],
            # 登录页链接
            "public_api"                => [
                "login"                 => "{$pluginPrefix}/admin/Publics/login",
                "user"                  => "{$pluginPrefix}/admin/Publics/user",
                "loginout"              => "{$pluginPrefix}/admin/Publics/loginout",
                "clear"                 => "{$pluginPrefix}/admin/Index/clear",
                "lock"                  => "{$pluginPrefix}/admin/Index/lock",
                "user_edit"             => "{$pluginPrefix}/admin/Admin/editSelf",
                "header_right_file"     => "{$pluginPrefix}/remote/header-toolbar",
            ],
            # 附件库
            "uploadify_api"             => [
                'index'                 => "{$pluginPrefix}/admin/Upload/index",
                'upload'                => "{$pluginPrefix}/admin/Upload/upload",
                'edit'                  => "{$pluginPrefix}/admin/Upload/edit",
                'del'                   => "{$pluginPrefix}/admin/Upload/del",
                'move'                  => "{$pluginPrefix}/admin/Upload/move",
            ],
            # 附件库分类
            'uploadify_cate' => [
                'index'                 => "{$pluginPrefix}/admin/UploadCate/index",
                'add'                   => "{$pluginPrefix}/admin/UploadCate/add",
                'edit'                  => "{$pluginPrefix}/admin/UploadCate/edit",
                'del'                   => "{$pluginPrefix}/admin/UploadCate/del",
            ],
        ];
        return $this->successRes(array_merge($data, $this->appendData));
    }

    /**
     * 应用登录
     * @param \support\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function login(Request $request)
    {
        // 获取数据
        $post = $request->post();
        // 数据验证
        hpValidate(SystemAdminValidate::class, $post, 'login');

        // 查询数据
        $where = [
            'saas_appid'        => $request->appid,
            'username'          => $post['username']
        ];
        $adminModel = $this->model->with(['role'])->where($where)->find();
        if (!$adminModel) {
            return $this->fail('登录账号错误');
        }
        // 验证登录密码
        if (!Password::passwordVerify((string) $post['password'], (string)$adminModel->password)) {
            return $this->fail('登录密码错误');
        }
        if ($adminModel->status != '20') {
            return $this->fail('该用户已被冻结');
        }
        Session::set($request->plugin, $adminModel);

        // 更新登录信息
        $ip = $request->ip();
        $adminModel->last_login_ip = $ip;
        $adminModel->last_login_time = date('Y-m-d H:i:s');
        $adminModel->save();

        // 返回数据
        return $this->successFul('登录成功', ['token' => Str::random(32)]);
    }

    /**
     * 用户信息
     * @param \support\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function user(Request $request)
    {
        $where = [
            'saas_appid'    => $request->appid,
            'id'            => $request->userId
        ];
        $adminModel = $this->model->with(['role'])->where($where)->find();
        if (!$adminModel) {
            throw new Exception('管理员不存在');
        }
        Session::set($request->plugin, $adminModel);
        # 获取菜单数据
        $menus = PluginMgr::getMenus($request->plugin);
        # 对菜单进行排序
        $menus = list_sort_by($menus, 'sort', 'asc');
        # 分配菜单数据
        $adminModel->menus = $menus;
        # 返回用户数据
        return $this->successRes($adminModel);
    }

    /**
     * 退出登录
     * @param \support\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function loginout(Request $request)
    {
        Session::delete($request->plugin);
        return $this->success("退出成功");
    }
}
