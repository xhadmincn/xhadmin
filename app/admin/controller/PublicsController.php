<?php

namespace app\admin\controller;

use app\common\manager\AuthMgr;
use app\common\manager\SettingsMgr;
use app\common\service\UploadService;
use app\common\utils\Password;
use Exception;
use loong\oauth\facade\Auth;
use support\Request;
use app\admin\model\SystemAdmin;
use app\common\service\SystemInfoService;
use app\admin\validate\SystemAdmin as ValidateSystemAdmin;
use app\common\BaseController;

class PublicsController extends BaseController
{
    /**
     * 不需要登录的方法
     * @var string[]
     */
    protected $noNeedLogin = ['site', 'login', 'captcha'];

    /**
     * 应用信息
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function site()
    {
        # 获取系统信息
        $systemInfo = SystemInfoService::info();
        # 获取模块名称
        $moduleName = getModule('admin');
        # 获取配置信息
        $config     = SettingsMgr::group(null,'system');
        # 获取网站名称
        $web_name   = empty($config['web_name']) ? 'XHAdmin' : $config['web_name'];
        $web_icp   = empty($config['web_icp']) ? '' : $config['web_icp'];
        $web_mps   = empty($config['web_mps']) ? '' : $config['web_mps'];
        $web_mps_text   = empty($config['web_mps_text']) ? '' : $config['web_mps_text'];
        # 获取网站Logo
        $web_logo   = empty($config['admin_logo']) ? '' : UploadService::url($config['admin_logo']);
        # 返回数据
        $data       = [
            'web_name'              => $web_name,
            'web_icp'               => $web_icp,
            'web_mps'               => $web_mps,
            'web_mps_text'          => $web_mps_text,
            'web_title'             => '总后台登录',
            'web_logo'              => $web_logo,
            'version_name'          => $systemInfo['system_version_name'],
            'version'               => $systemInfo['system_version'],
            // 版权token
            'empower_token'         => $systemInfo['site_encrypt'],
            // 版权私钥
            'empower_private_key'   => $systemInfo['privatekey'],
            // 登录页链接
            'login_link'            => [
                'register'          => '',
                'forget'            => '',
                'other_login'       => [],
            ],
            // 公用接口
            'public_api'            => [
                // 登录接口
                'login'             => "{$moduleName}/Publics/login",
                // 自定义登录页
                'login_file'        => '',
                // 退出接口
                'loginout'          => "{$moduleName}/Publics/loginout",
                // 获取用户信息
                'user'              => "{$moduleName}/Publics/user",
                // 获取权限菜单
                'menus'             => "{$moduleName}/Publics/menus",
                // 清除缓存
                'clear'             => "{$moduleName}/Index/clear",
                // 锁定页面
                'lock'              => "{$moduleName}/Index/lock",
                // 修改登录者信息
                "user_edit"         => "{$moduleName}/SystemAdmin/editSelf",
                // 头部toolBar远程文件
                "header_right_file" => "remote/header-toolbar",
            ],
            // 远程组件
            'remote_url'            => [],
            // 附件库API
            'uploadify_api'         => [
                'index'             => "{$moduleName}/SystemUpload/index",
                'upload'            => "{$moduleName}/SystemUpload/upload",
                'edit'              => "{$moduleName}/SystemUpload/edit",
                'del'               => "{$moduleName}/SystemUpload/del",
                'move'              => "{$moduleName}/SystemUpload/move",
            ],
            // 附件库分类
            'uploadify_cate'        => [
                'index'             => "{$moduleName}/SystemUploadCate/index",
                'add'               => "{$moduleName}/SystemUploadCate/add",
                'edit'              => "{$moduleName}/SystemUploadCate/edit",
                'del'               => "{$moduleName}/SystemUploadCate/del",
            ],
        ];
        return $this->successRes($data);
    }

    /**
     * 系统登录
     * @param Request $request
     * @throws Exception
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function login(Request $request)
    {
        // 获取数据
        $post = $request->post();
        // 数据验证
        hpValidate(ValidateSystemAdmin::class, $post, 'login');

        // 查询数据
        $where      = [
            'username' => $post['username']
        ];
        $adminModel = SystemAdmin::with(['role'])->where($where)->find();
        if (!$adminModel) {
            throw new Exception('登录账号错误');
        }
        // 验证登录密码
        if (!Password::passwordVerify((string) $post['password'], (string) $adminModel->password)) {
            throw new Exception('登录密码错误');
        }
        if ($adminModel->status == 0) {
            throw new Exception('该用户已被冻结');
        }        
        // 更新登录信息
        $ip                          = $request->ip();
        $adminModel->last_login_ip   = $ip;
        $adminModel->last_login_time = date('Y-m-d H:i:s');
        $adminModel->save();

        // 构建令牌
        $data  = $adminModel->toArray();
        $token = Auth::encrypt($data);

        // 返回数据
        return $this->successToken('登录成功', $token);
    }


    /**
     * 获取管理员数据
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function user(Request $request)
    {
        $data = $request->user;
        $data['menus'] = $this->getMenus();
        $data['theme'] = [
            'layoutMenu'        => 'level',
        ];
        return parent::successRes($data);
    }

    /**
     * 获取菜单数据
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    private function getMenus()
    {
        $admin  = $this->request->user;
        $data   = AuthMgr::run($admin);
        return $data;
    }

    /**
     * 退出登录
     * @param Request $request
     * @return \support\Response
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public function loginout(Request $request)
    {
        return $this->success('成功退出');
    }
}
