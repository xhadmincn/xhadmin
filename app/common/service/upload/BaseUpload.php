<?php

namespace app\common\service\upload;

use app\common\manager\SettingsMgr;
use app\common\model\SystemUpload;
use app\common\model\SystemUploadCate;
use app\common\validate\AliyunValidate;
use app\common\validate\QcloudValidate;
use app\common\validate\QiniuValidate;
use Exception;
use think\facade\Config;
use yzh52521\filesystem\facade\Filesystem;

/**
 * 附件上传基础类
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
trait BaseUpload
{
    /**
     * 站点ID
     * @var null|int
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected static $store_id = null;

    /**
     * 应用ID
     * @var null|int
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected static $saas_appid = null;

    /**
     * 用户ID
     * @var null|int
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected static $uid = null;

    /**
     * 检测文件是否已存在
     * @param mixed $fileName
     * @param mixed $adapter
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getFileInfo($fileName, $adapter)
    {
        $where['filename'] = $fileName;
        $where['adapter']  = $adapter;
        if (self::$saas_appid) {
            $where['saas_appid'] = self::$saas_appid;
        }
        if (self::$store_id) {
            $where['store_id'] = self::$store_id;
        }
        $fileModel = SystemUpload::where($where)->find();
        if ($fileModel) {
            $fileModel->update_at = date('Y-m-d H:i:s');
            $fileModel->save();
            return $fileModel->toArray();
        }
        return [];
    }

    /**
     * 获取储存的分类
     * @param string $dir_name
     * @param mixed $appid
     * @param mixed $store_id
     * @throws \Exception
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected static function getCategory(string $dir_name): array
    {
        $where = [];
        if ($dir_name) {
            $where[] = ['dir_name', '=', $dir_name];
        } else {
            $where[] = ['is_system', '=', '20'];
        }
        if (self::$saas_appid) {
            $where[] = ['saas_appid', '=', self::$saas_appid];
        }
        if (self::$store_id) {
            $where[] = ['store_id', '=', self::$store_id];
        }
        if (self::$uid) {
            $where[] = ['uid', '=', self::$uid];
        }
        $category = SystemUploadCate::where($where)->find();
        if (!$category) {
            $category = SystemUploadCate::order(['id' => 'asc'])->find();
        }
        if (!$category) {
            throw new Exception('没有更多的附件分类可用');
        }
        return $category->toArray();
    }

    /**
     * 获取附件库配置项
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getConfig()
    {
        if (self::$saas_appid) {
            # 应用级附件库
            $config = SettingsMgr::group(self::$saas_appid, 'upload', []);
        } else {
            # 系统级附件库
            $config = SettingsMgr::group(null, 'upload', []);
        }
        if (empty($config['upload_drive'])) {
            throw new Exception('附件库驱动未设置', 13000);
        }
        if (empty($config['children'])) {
            throw new Exception('请先设置附件库', 13000);
        }
        return $config;
    }

    /**
     * 获取当前使用配置项
     * @param mixed $drive
     * @throws \Exception
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getCurrentConfig($drive = '')
    {
        $config = self::getConfig();
        # 当前使用驱动
        if (empty($drive)) {
            $drive = $config['upload_drive'];
        }
        # 附件库配置
        $settings = isset($config['children'][$drive]) ? $config['children'][$drive] : [];
        if (empty($config['children'])) {
            throw new Exception('请先设置附件库上传设置', 13000);
        }
        return $settings;
    }

    /**
     * 获取当前使用驱动
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getDrive()
    {
        $config = self::getConfig();
        return $config['upload_drive'] ?? '';
    }

    /**
     * 获取驱动SDK
     * @param mixed $drive
     * @return \yzh52521\filesystem\Driver
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getDisk($drive = '')
    {
        # 获取配置项
        $config = self::getConfig();
        # 设置驱动
        if (empty($drive)) {
            $drive = $config['upload_drive'];
        }
        # 当前使用附件库配置
        $settings = [];
        # 获取全部配置项
        $configOptions = $config['children'];
        # 合并配置
        $templateConfig = config("filesystem.disks", []);
        foreach ($configOptions as $key => $value) {
            $configSave     = array_merge($templateConfig[$key] ?? [], $value);
            $settings[$key] = $configSave;
        }
        # 取验证数据
        $settingConf = empty($settings[$drive]) ? [] : $settings[$drive];
        # 阿里云驱动验证
        if ($drive === 'aliyun') {
            hpValidate(AliyunValidate::class, $settingConf);
        }
        # 腾讯云驱动
        if ($drive === 'qcloud') {
            hpValidate(QcloudValidate::class, $settingConf);
        }
        # 七牛云驱动
        if ($drive === 'qiniu') {
            hpValidate(QiniuValidate::class, $settingConf);
        }
        # 动态设置配置
        Config::set([
            'default' => $drive,
            'disks' => $settings
        ], 'filesystem');
        # 获取驱动SDK
        return Filesystem::disk($drive);
    }


    /**
     * 保存文件信息
     * @param string $path
     * @param string $dir_name
     * @param int $store_id
     * @param int $appid
     * @param int $uid
     * @param string $drive
     * @return SystemUpload
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function addUpload(string $path, string $dir_name, int $store_id = null, int $appid = null, int $uid = null, string $drive = 'local')
    {
        # 完整地址
        $fullPath = public_path() . $path;
        # 获取文件信息
        $info = pathinfo($fullPath);
        $size = filesize($fullPath);
        # 获取分类
        $category = self::getCategory($dir_name);
        # 组装数据
        $data = [
            'cid'           => $category['id'],
            'store_id'      => $store_id,
            'saas_appid'    => $appid,
            'uid'           => $uid,
            'title'         => $info['basename'],
            'filename'      => $info['filename'],
            'format'        => $info['extension'],
            'adapter'       => $drive,
            'size'          => get_size($size),
            'path'          => $path,
        ];
        $model = new SystemUpload;
        if (!$model->save($data)) {
            throw new Exception('保存文件信息失败');
        }
        return $model;
    }

    /**
     * 设置站点ID
     * @param null|int $id
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function setStoreId(null|int $id)
    {
        self::$store_id = $id;
    }

    /**
     * 设置应用ID
     * @param null|int $id
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function setSaasAppid(null|int $id)
    {
        self::$saas_appid = $id;
    }

    /**
     * 设置用户ID
     * @param null|int $id
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function setUid(null|int $id)
    {
        self::$uid = $id;
    }
}
