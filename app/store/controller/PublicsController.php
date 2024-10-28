<?php

namespace app\store\controller;

use app\common\service\SystemInfoService;
use app\common\model\Store;
use app\common\service\UploadService;
use app\store\model\StoreMenus;
use app\common\utils\Password;
use Exception;
use loong\oauth\facade\Auth;
use support\Request;
use app\admin\validate\SystemAdmin as ValidateSystemAdmin;
use app\common\BaseController;
use think\facade\Session;

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
    public function site(Request $request)
    {
        $admin_logo = getHpConfig('admin_logo');
        $admin_logo = is_array($admin_logo) ? current($admin_logo) : $admin_logo;
        $web_logo = UploadService::url($admin_logo);
        $systemInfo = SystemInfoService::info();
        $config=getHpConfig('',null,'system');
        $web_name=empty($config['web_name']) ? 'XHAdmin' : $config['web_name'];
        $web_icp   = empty($config['web_icp']) ? '' : $config['web_icp'];
        $web_mps   = empty($config['web_mps']) ? '' : $config['web_mps'];
        $web_mps_text   = empty($config['web_mps_text']) ? '' : $config['web_mps_text'];
        $data = [
            'web_name' => $web_name,
            'web_icp' => $web_icp,
            'web_mps' => $web_mps,
            'web_mps_text' => $web_mps_text,
            'web_title' => '后台登录',
            'web_logo' => empty($web_logo) ? '' : $web_logo,
            'version_name' => $systemInfo['system_version_name'],
            'version' => $systemInfo['system_version'],
            // 版权token
            'empower_token' => $systemInfo['site_encrypt'],
            // 版权私钥
            'empower_private_key' => $systemInfo['privatekey'],
            // 登录页链接
            'login_link' => [
                'register' => '',
                'forget' => '',
                'other_login' => [],
            ],
            // 公用接口
            'public_api' => [
                'login' => "{$this->moduleName}/Publics/login",
                'loginout' => "{$this->moduleName}/Publics/loginout",
                'user' => "{$this->moduleName}/Publics/user",
                'menus' => "{$this->moduleName}/Publics/menus",
                'clear' => "{$this->moduleName}/Index/clear",
                'lock' => "{$this->moduleName}/Index/lock",
                "user_edit" => "{$this->moduleName}/Store/edit",
            ],
            # 自定义登录页（配合remote_url使用）
            'login_url' => '/store/login',
            // 远程组件
            'remote_url' => [
                [
                    'title' => '用户登录',
                    'path' => '/store/login',
                    'remote' => 'remote/store/login'
                ],
            ],
            // 附件库API
            'uploadify_api' => [
                'index' => "{$this->moduleName}/SystemUpload/index",
                'upload' => "{$this->moduleName}/SystemUpload/upload",
                'edit' => "{$this->moduleName}/SystemUpload/edit",
                'del' => "{$this->moduleName}/SystemUpload/del",
                'move' => "{$this->moduleName}/SystemUpload/move",
            ],
            // 附件库分类
            'uploadify_cate' => [
                'index' => "{$this->moduleName}/SystemUploadCate/index",
                'add' => "{$this->moduleName}/SystemUploadCate/add",
                'edit' => "{$this->moduleName}/SystemUploadCate/edit",
                'del' => "{$this->moduleName}/SystemUploadCate/del",
            ],
        ];
        return parent::successRes($data);
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
        $where = [
            'username' => $post['username']
        ];
        $adminModel = Store::where($where)->find();
        if (!$adminModel) {
            return $this->fail('登录账号错误');
        }
        // 验证登录密码
        if (!Password::passwordVerify((string) $post['password'], (string) $adminModel->password)) {
            return $this->fail('登录密码错误');
        }
        // 判断状态
        if ($adminModel->status === '10') {
            return $this->fail('该用户已被冻结');
        }

        // 更新登录信息
        $ip = $request->ip();
        $adminModel->last_login_ip = $ip;
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
        $user = $request->user;
        # 前端数据
        $data = [
            'id'                => $user['id'],
            'username'          => $user['username'],
            'nickname'          => $user['title'],
            'headimg'           => $user['logo'],
            'plugins'           => $user['plugins_name'],
            'menus'             => $this->getMenus(),
            // 主题配置（待扩展）
            'theme'             => [
                'layout'        => 'top',
                'layoutSize'    => false,
            ],
        ];
        return $this->successRes($data);
    }

    /**
     * 获取菜单数据
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    private function getMenus()
    {
        $order = [
            'sort' => 'asc',
            'id' => 'asc'
        ];
        $field = [
            'id',
            'pid',
            'path',
            'title',
            'method',
            'component',
            'auth_params',
            'icon',
            'show',
        ];
        $data = StoreMenus::order($order)
        ->field($field)
        ->select()
        ->each(function ($e) {
            $e->show = $e->show === '20' ? '1' : '0';
            if ($e->component === 'none/index') {
                $e->component = '';
            }
            is_array($e->method) && $e->method = current($e->method);
            return $e;
        })
        ->toArray();
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