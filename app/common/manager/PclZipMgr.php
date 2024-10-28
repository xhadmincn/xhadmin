<?php
namespace app\common\manager;

use Exception;

/**
 * 使用PclZip扩展管理器
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class PclZipMgr
{
    /**
     * 构造函数
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function __construct()
    {
        if (!class_exists("PclZip")) {
            throw new Exception('请先安装composer扩展包pclzip/pclzip');
        }
    }

    /**
     * 打包某目录下所有文件
     * @param string $zipFilePath 打包至目标压缩包
     * @param string $extractTo 打包目标路径
     * @param array $ignoreFiles 需要忽略的绝对目录路径或者文件（可选）
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function build(string $zipFilePath, string $extractTo, array $ignoreFiles = [])
    {
        $zip        = new \PclZip($zipFilePath);
        $result = $zip->create('file.txt,data/text.txt,folder');
        if ($openStatus !== true) {
            throw new Exception('打包失败');
        }
        # 执行递归打包
        self::addFileToZip($zip, $extractTo, '/', $ignoreFiles);
    }

    /**
     * 打包所需文件
     * @param string $zipFilePath
     * @param array $files
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function buildFiles(string $zipFilePath,string $extractTo, array $files)
    {
        $zip        = new \PclZip($zipFilePath);
        $openStatus = $zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($openStatus !== true) {
            throw new Exception('打包失败');
        }
        # 执行递归打包
        foreach ($files as $file) {
            $filePath = "{$extractTo}/{$file}";
            if (is_file($filePath) && file_exists($filePath)) {
                $zip->addFile($filePath, $file);
            }else if(is_dir($filePath)){
                $this->addFileToZip($zip, $filePath, "{$file}/");
            }
        }
    }

    /**
     * 扫描目录并添加文件至压缩包
     * @param \ZipArchive $zip
     * @param string $sourcePath
     * @param string $zipPath
     * @param array $ignoreFiles
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    private function addFileToZip(ZipArchive $zip, string $sourcePath, string $zipPath = '/', array $ignoreFiles = [])
    {
        $files = scandir($sourcePath);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                $path = $sourcePath . DIRECTORY_SEPARATOR . $file;
                if (!in_array($path, $ignoreFiles)) {
                    if (is_dir($path)) {
                        $zip->addEmptyDir($zipPath . $file);
                        $this->addFileToZip($zip, $path, $zipPath . $file . DIRECTORY_SEPARATOR);
                    } else if(is_file($path) && file_exists($path)){
                        $zip->addFile($path, $zipPath . $file);
                    }
                }
            }
        }
    }

    /**
     * 解压目标压缩包至目录
     * @param string $zipFilePath 压缩包路径
     * @param string $tarGetPath 解压至目标路径
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function unzip(string $zipFilePath, string $tarGetPath)
    {
        # 检测压缩包是否存在
        if (!file_exists($zipFilePath)) {
            throw new Exception('压缩包不存在'. $zipFilePath);
        }
        # 检测目录不存在则创建
        if (!is_dir($tarGetPath)) {
            mkdir($tarGetPath, 0777, true);
        }
        $zip        = new \PclZip($zipFilePath);
        $openStatus = $zip->open($zipFilePath);
        if ($openStatus !== true) {
            throw new Exception('解压失败');
        }
        # 解压至目标目录
        $zip->extractTo($tarGetPath);
        # 关闭资源
        $zip->close();
    }
}