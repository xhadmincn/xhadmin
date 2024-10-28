<?php
namespace app\common\manager;

use app\common\manager\StoreAppMgr;
use app\common\utils\Password;
use Exception;
use app\common\model\Users;

/**
 * 用户管理器
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-05-20
 */
class UsersMgr
{
    /**
     * 用户登录
     * @param int $appid
     * @param string $username
     * @param string $password
     * @throws \Exception
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function login(int $appid, string $username, string $password)
    {
        # 获取应用数据
        $app = StoreAppMgr::detail(['id' => $appid]);

        # 查询数据
        $where     = [
            'username' => $username,
            'saas_appid' => $app['id'],
            'store_id' => $app['store_id']
        ];
        $userModel = Users::where($where)->find();
        if (!$userModel) {
            throw new Exception('登录账号错误');
        }
        # 验证登录密码
        if (!Password::passwordVerify((string) $password, (string) $userModel->password)) {
            throw new Exception('登录密码错误');
        }
        if ($userModel->status === '10') {
            throw new Exception('该用户已被冻结');
        }
        $user    = $userModel->toArray();
        $request = request();
        $session = $request->session();
        $session->set($app['name'], $user);

        # 更新登录信息
        $ip                         = $request->getRealIp($safe_mode = true);
        $userModel->last_login_ip   = $ip;
        $userModel->last_login_time = date('Y-m-d H:i:s');
        if (!$userModel->save()) {
            throw new Exception('用户登录失败');
        }
        $user['token'] = $request->sessionId();
        return $user;
    }

    /**
     * 退出登录
     * @param string $appid
     * @return bool
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function loginout(int $appid)
    {
        # 获取应用数据
        $app = StoreAppMgr::detail(['id' => $appid]);
        if (empty($app)) {
            throw new Exception('该项目不存在');
        }
        # 删除登录信息
        $session = request()->session();
        $session->delete($app['name']);
        return true;
    }

    /**
     * 注册用户
     * @param array $data
     * @return \app\common\model\Users
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function add(array $data): Users
    {
        if (empty($data['saas_appid'])) {
            throw new Exception('缺少项目ID');
        }
        # 查询项目
        $app = StoreAppMgr::detail(['id' => $data['saas_appid']]);
        if (!$app) {
            throw new Exception('该应用不存在');
        }
        $where = [
            'store_id'      => $app['store_id'],
            'saas_appid'    => $app['id'],
            'username'      => $data['username'],
        ];
        $userModel = Users::where($where)->find();
        if ($userModel) {
            return $userModel;
        }
        $data['store_id'] = $app['store_id'];
        $model            = new Users;
        if (!$model->save($data)) {
            throw new Exception('注册用户失败');
        }
        return $model;
    }

    /**
     * 修改用户
     * @param array $where
     * @param array $data
     * @return \app\common\model\Users
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function edit(array $where, array $data): Users
    {
        if (empty($where['saas_appid'])) {
            throw new Exception('缺少项目ID');
        }
        $model = Users::where($where)->find();
        if (!$model) {
            throw new Exception('该用户未注册');
        }
        if (!$model->save($data)) {
            throw new Exception('修改用户失败');
        }
        return $model;
    }

    /**
     * 删除用户
     * @param array $where
     * @throws Exception
     * @return bool
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-20
     */
    public static function del(array $where)
    {
        if (empty($where['saas_appid'])) {
            throw new Exception('缺少项目ID');
        }
        $model = self::detail($where);
        if (!$model->delete()) {
            throw new Exception('删除用户失败');
        }
        return true;
    }

    /**
     * 修改登录账号
     * @param array $where
     * @param string $username
     * @throws Exception
     * @return bool
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-20
     */
    public static function setUsername(array $where, string $username)
    {
        if (empty($where['saas_appid'])) {
            throw new Exception('缺少项目ID');
        }
        $model           = self::detail($where);
        $model->username = $username;
        if (!$model->save()) {
            throw new Exception('修改用户账号失败');
        }
        return true;
    }

    /**
     * 修改登录密码
     * @param array $where
     * @param string $password
     * @throws Exception
     * @return bool
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-20
     */
    public static function setPassword(array $where, string $password)
    {
        if (empty($where['saas_appid'])) {
            throw new Exception('缺少项目ID');
        }
        $model           = self::detail($where);
        $model->password = $password;
        if (!$model->save()) {
            throw new Exception('修改用户密码失败');
        }
        return true;
    }

    /**
     * 修改用户状态
     * @param array $where
     * @param string $status
     * @throws Exception
     * @return bool
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-20
     */
    public static function setStatus(array $where, string $status)
    {
        if (empty($where['saas_appid'])) {
            throw new Exception('缺少项目ID');
        }
        $model         = self::detail($where);
        $model->status = $status;
        if (!$model->save()) {
            throw new Exception('修改用户状态失败');
        }
        return true;
    }

    /**
     * 获取用户详情
     * @param array $where
     * @return \app\common\model\Users
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function detail(array $where): Users
    {
        if (empty($where['saas_appid'])) {
            throw new Exception('缺少项目ID');
        }
        $model = Users::where($where)->find();
        if (!$model) {
            throw new Exception('该用户未注册');
        }
        return $model;
    }
}