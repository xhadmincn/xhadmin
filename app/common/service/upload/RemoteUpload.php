<?php

namespace app\common\service\upload;

use Exception;
use think\file\UploadedFile;

/**
 * 附件服务类
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-04
 */
trait RemoteUpload
{
    # 使用基础类
    use BaseUpload;

    /**
     * 下载远程文件并上传至附件库
     * @param string $url
     * @param string $dir_name
     * @param mixed $appid
     * @param mixed $uid
     * @param mixed $store_id
     * @throws \Exception
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function remoteFile(string $url, string $dir_name = '', $appid = null, $uid = null,$store_id = null): array
    {
        $fileInfo = pathinfo($url);
        if (!isset($fileInfo['extension'])) {
            $extension = 'png';
        }
        if (!isset($fileInfo['filename'])) {
            throw new Exception('获取远程文件名称失败');
        }
        $fileMd5  = md5($fileInfo['filename']);
        # 储存临时缓存文件目录
        $tempFile = runtime_path() . "tempDown/{$fileMd5}.{$extension}";
        if (!is_dir(dirname($tempFile))) {
            mkdir(dirname($tempFile), 0775, true);
        }
        # 读取远程文件
        $file = file_get_contents($url);
        if (!file_put_contents($tempFile, $file)) {
            throw new Exception('远程资源文件下载失败');
        }
        $fileMimeType = self::getFileMimeType($tempFile);
        $fileName = "{$fileInfo['basename']}.{$extension}";
        $uploadFile   = new UploadedFile($tempFile, $fileName, $fileMimeType);
        if (!$data = self::upload($uploadFile, $dir_name, $appid, $uid,$store_id)) {
            throw new Exception('上传文件失败');
        }
        return $data;
    }
}