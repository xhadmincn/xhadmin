<?php

namespace app\common\service;

use app\common\model\SystemUpload;
use app\common\service\upload\BaseUpload;
use app\common\service\upload\RemoteUpload;
use Exception;
use think\facade\Log;
use think\file\UploadedFile;

/**
 * 附件服务类
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-04
 */
class UploadService
{
    # 使用基础类
    use BaseUpload;
    # 使用远程文件下载并储存
    use RemoteUpload;

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
    protected static $acceptExt = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'mp3', 'mp4', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'pem', 'crt', 'key', 'zip', 'rar', '7z', 'ttf', 'otf', 'woff', 'woff2', 'eot'];

    /**
     * 上传文件
     * @param \think\file\UploadedFile $file
     * @param string $dir_name 上传目录(分类dir_name)
     * @param mixed $appid 上传应用ID
     * @param mixed $uid 上传用户ID
     * @param mixed $store_id 上传站点ID
     * @throws \Exception
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function upload(UploadedFile $file, string $dir_name = '', $appid = null, $uid = null, $store_id = null, $is_hide = 0,$acceptExt=[])
    {
        if(empty($acceptExt)){
            $acceptExt = self::$acceptExt;
        }
        validate(['image'=>'fileExt:'.implode(',',$acceptExt)])
            ->check([
                'image' => $file
            ]);
        # 设置站点ID
        self::setStoreId($store_id);
        # 设置应用ID
        self::setSaasAppid($appid);
        # 设置用户ID
        self::setUid($uid);
        # 获取分类
        $category = self::getCategory($dir_name);
        # 获取文件后缀
        $extension = $file->extension();
        # 必须储存本地文件后缀
        $suffix = config('localsuffix');
        $suffix = empty($suffix) ? [] : $suffix;
        # 使用附件驱动
        $drive    = self::getDrive();
        # 检测是否必须储存本地
        if (in_array($extension, $suffix)) {
            $drive = 'local';
        }
        # 获取驱动SDK
        $filesystem = self::getDisk($drive);
        # 组装数据
        $data               = [
            'cid'           => $category['id'],
            'store_id'      => $store_id,
            'saas_appid'    => $appid,
            'uid'           => $uid,
            'title'         => $file->getOriginalName(),
            'filename'      => $file->getOriginalName(),
            'format'        => $extension,
            'adapter'       => $drive,
            'size'          => '',
            'path'          => '',
            'url'           => '',
            'hide'          => $is_hide
        ];

        # 检测文件是否存在
        /* if ($info = self::getFileInfo($data['filename'], $data['adapter'])) {
            $data['size'] = $info['size'];
            $data['path'] = $info['path'];
            $data['url']  = $filesystem->url($info['path']);
            # 返回数据
            return $data;
        } */
        # 上传子目录
        $dirName = $category['dir_name'] ?? 'default';
        # 上传文件
        $path = $filesystem->putFile($dirName, $file);
        # 本地附件库(重设储存路径)
        $localPath = $path;
        if ($data['adapter'] === 'local') {
            $config = self::getCurrentConfig($drive);
            $localPath = "{$config['root']}/{$path}";
        }
        $data['path']           = $localPath;
        $data['size']           = get_size($filesystem->size($path));
        $data['url']            = $filesystem->url($localPath);
        # 保存数据
        $model = new SystemUpload;
        if (!$model->save($data)) {
            throw new Exception('附件上传失败');
        }
        # 返回数据
        return $data;
    }

    /**
     * 获取文件fileMime
     * @param mixed $file_path
     * @return bool|string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private static function getFileMimeType($file_path)
    {
        $finfo     = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_path);
        finfo_close($finfo);
        return $mime_type;
    }

    /**
     * 删除系统附件
     * @param int $id
     * @param mixed $force
     * @return bool
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public static function delete(int $id, $force = false): bool
    {
        return SystemUpload::destroy($id, $force);
    }

    /**
     * 获取外链地址
     * @param mixed $path
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function url(mixed $path)
    {
        if (is_array($path)) {
            $data = [];
            foreach ($path as $key => $value) {
                $data[$key] = self::url($value);
            }
            return $data;
        }
        if (empty($path) || is_null($path)) {
            return null;
        }
        $model = self::model($path);
        if (!$model) {
            return '';
        }
        try {
            # 设置数据
            self::setSaasAppid($model['saas_appid']);
            self::setStoreId($model['store_id']);
            self::setUid($model['uid']);
            # 获取驱动SDK
            $disk = self::getDisk($model['adapter']);
            # 访问链接
            $url = '';
            # 是否私有空间
            $private_type = config('filesystem.disks.' . $model['adapter'] . '.private_type', '10');
            if ($private_type === '20') {
                # 过期时间（单位：秒）
                $expire_time = 600;
                switch ($model['adapter']) {
                    case 'aliyun':
                        /** @var \yzh52521\Flysystem\Oss\OssAdapter */
                        $cosDisk = $disk->getAdapter();
                        # 获取临时链接（一个小时有效）
                        $url = $cosDisk->getTemporaryUrl($path, $expire_time);
                        break;
                    case 'qcloud':
                        /** @var \Overtrue\Flysystem\Cos\CosAdapter */
                        $cosDisk = $disk->getAdapter();
                        # 获取临时链接（一个小时有效）
                        $expire_time = $expire_time / 10;
                        $url = $cosDisk->getTemporaryUrl($path, "+{$expire_time} minutes");
                        break;
                    case 'qiniu':
                        /** @var \Overtrue\Flysystem\Qiniu\QiniuAdapter */
                        $qiniuDisk = $disk->getAdapter();
                        # 获取临时链接（一个小时有效）
                        $url = $qiniuDisk->getTemporaryUrl($path, $expire_time);
                        break;
                    default:
                        # 驱动错误，尝试换取普通链接
                        $url  = $disk->url($path);
                        break;
                }
            } else {
                # 换取普通链接
                $url  = $disk->url($path);
            }
            return $url;
        } catch (\Throwable $e) {
            Log::error("附件外链获取失败：{$e->getMessage()}");
            return $path;
        }
    }

    /**
     * 批量获取URL外链
     * @param array $data
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public static function urls(array $array)
    {
        $data = [];
        foreach ($array as $value) {
            $data[] = self::url($value);
        }
        return $data;
    }

    /**
     * 查询模型数据
     * @param string $path
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function model(string $path)
    {
        $where = [
            'path'      => $path,
        ];
        $model = SystemUpload::where($where)->find();
        if (!$model) {
            return '';
        }
        return $model;
    }

    /**
     * URL转PATH
     * @param string|array $url
     * @param bool $array 是否返回数组
     * @return array|string
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public static function path(string|array $url,bool $array = false)
    {
        if (is_array($url)) {
            $data = [];
            if (count($url) === 1&&!$array) {
                return self::path(current($url));
            }
            foreach ($url as $value) {
                if (filter_var($value, FILTER_SANITIZE_URL) === false) {
                    throw new Exception('URL地址不合法');
                }
                $parseUrl = parse_url($value);
                $data[]   = ltrim($parseUrl['path'], '/');
            }
            return $data;
        } else {
            if (filter_var($url, FILTER_SANITIZE_URL) === false) {
                throw new Exception('URL地址不合法');
            }
            $parseUrl = parse_url($url);
            $data     = ltrim($parseUrl['path'], '/');
            return $data;
        }
    }
}
