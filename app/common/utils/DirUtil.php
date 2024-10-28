<?php

namespace app\common\utils;

use Exception;
use think\facade\Log;

/**
 * 目录操作类
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
final class DirUtil
{
    /**
     * 目录名
     * @param mixed $dir_name
     * @return array|string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    static public function dirPath($dir_name)
    {
        $dirname = str_ireplace("\\", "/", $dir_name);
        return substr($dirname, "-1") == "/" ? $dirname : $dirname . "/";
    }

    /**
     * 获取文件扩展名
     * @param string $file
     * @return string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    static public function getExt(string $file)
    {
        return strtolower(substr(strrchr($file, "."), 1));
    }

    /**
     * 获取目录树
     * @param string $dirName 目录名
     * @param mixed $exts 读取的文件扩展名（支持数组与字符串，字符串用|隔开）
     * @param mixed $son 是否显示子目录
     * @param mixed $list 目录列表
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    static public function tree2(string $dirName = null, $exts = '', $son = 0, $list = array())
    {

        if (is_null($dirName))
            $dirName = '.';
        $dirPath = self::dirPath($dirName);
        if (is_array($exts))
            $exts = implode("|", $exts);
        foreach (glob($dirPath . '*') as $v) {
            if (is_dir($v) || !$exts || preg_match("/\.($exts)/i", $v)) {
                $path   = str_replace("\\", "/", realpath($v)) . (is_dir($v) ? '/' : '');
                $list[] = [
                    'type' => filetype($v),
                    'filename' => basename($v),
                    'path' => $path,
                    // 'spath' => ltrim(str_replace(dirname($_SERVER['SCRIPT_FILENAME']), '', $path), '/'),
                    'filemtime' => filemtime($v),
                    'fileatime' => fileatime($v),
                    'size' => is_file($v) ? filesize($v) : self::get_dir_size($v),
                    'iswrite' => is_writeable($v) ? 1 : 0,
                    'isread' => is_readable($v) ? 1 : 0,
                    'data' => $son && is_dir($v) ? self::tree($v, $exts, $son = 1, $list) : []
                ];
            }
        }
        return $list;
    }

    /**
     * 复制目录下所有文件至目标文件夹下
     * @param string $dirPath
     * @param string $targetPath
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function copyDir(string $dirPath, string $targetPath)
    {
        $data = self::tree($dirPath, true);
        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0777, true);
        }
        # 开始复制文件及目录
        self::copyFile($data, $targetPath);
    }

    /**
     * 递归复制文件
     * @param array $data
     * @param string $targetPath
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    private static function copyFile(array $data, string $targetPath)
    {
        foreach ($data as $value) {
            if (empty($value['sitePath'])) {
                continue;
            }
            $path = $targetPath . DIRECTORY_SEPARATOR . $value['sitePath'];
            if (is_dir($value['path']) && !is_dir($path) && !file_exists($path)) {
                mkdir($path, 0777, true);
            }
            if (is_file($value['path'])) {
                copy($value['path'], $path);
            }
            # 继续复制子目录与文件
            if (!empty($value['children'])) {
                self::copyFile($value['children'], $targetPath);
            }
        }
    }

    /**
     * 获取目录树
     * @param string $dirPath
     * @param bool $son
     * @param mixed $exts
     * @param string $parent
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function tree(string $dirPath, bool $son = false, $exts = '', string $parent = '')
    {
        if (is_array($exts)) {
            $exts = implode(",", $exts);
        }
        $list = [];
        if (is_dir($dirPath)) {
            $dirs = array_diff(scandir($dirPath), ['.', '..']);
            foreach ($dirs as $file) {
                # 检查文件扩展名
                if ($exts && !preg_match("/\.($exts)/i", $file)) {
                    continue;
                }
                $filePath = $dirPath . DIRECTORY_SEPARATOR . $file;
                $sitePath = $parent ? $parent . DIRECTORY_SEPARATOR . $file : $parent . $file;
                $item     = [
                    'file' => $file,
                    'path' => $filePath,
                    "sitePath" => $sitePath,
                    'children' => $son && is_dir($filePath) ? self::tree($filePath, $son, $exts, $sitePath) : [],
                ];
                array_push($list, $item);
            }
        }
        return $list;
    }

    /**
     * 遍历目录内容【时间控制】
     * @param string $dirName 目录名
     * @param string $exts 读取的文件扩展名
     * @param int $son 是否显示子目录
     * @param array $list
     * @return array
     */
    static public function treeTime($dirName = null, $startDate = 0, $endDate = 0, $exts = '', $son = 0, $list = array())
    {

        if (is_null($dirName))
            $dirName = '.';
        $dirPath = self::dirPath($dirName);
        if (is_array($exts))
            $exts = implode("|", $exts);
        foreach (glob($dirPath . '*') as $v) {
            if (filemtime($v) > $startDate && filemtime($v) < $endDate) {
                if (is_dir($v) || !$exts || preg_match("/\.($exts)/i", $v)) {
                    /*$list [$id] ['type'] = filetype($v);
                        $list [$id] ['filename'] = basename($v);
                        $path = str_replace("\\", "/", realpath($v)) . (is_dir($v) ? '/' : '');
                        $list [$id] ['path']=$path;
                        $list [$id] ['spath']=ltrim(str_replace(dirname($_SERVER['SCRIPT_FILENAME']),'',$path),'/');
                        $list [$id] ['filemtime'] = filemtime($v);
                        $list [$id] ['fileatime'] = fileatime($v);
                        $list [$id] ['size'] = is_file($v) ? filesize($v) : self::get_dir_size($v);
                        $list [$id] ['iswrite'] = is_writeable($v) ? 1 : 0;
                        $list [$id] ['isread'] = is_readable($v) ? 1 : 0;*/
                    $path   = str_replace("\\", "/", realpath($v)) . (is_dir($v) ? '/' : '');
                    $list[] = [
                        'type' => filetype($v),
                        'filename' => basename($v),
                        'path' => $path,
                        'spath' => ltrim(str_replace(dirname($_SERVER['SCRIPT_FILENAME']), '', $path), '/'),
                        'filemtime' => filemtime($v),
                        'fileatime' => fileatime($v),
                        'size' => is_file($v) ? filesize($v) : self::get_dir_size($v),
                        'iswrite' => is_writeable($v) ? 1 : 0,
                        'isread' => is_readable($v) ? 1 : 0,
                        'data' => $son && is_dir($v) ? self::tree($v, $exts, $son = 1, $list) : []
                    ];
                }
            }
        }
        return $list;
    }

    /**
     * 获取目录大小
     * @param mixed $dirPath
     * @return float|int
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    static public function get_dir_size($dirPath)
    {
        $s = 0;
        foreach (glob($dirPath . '/*') as $v) {
            $s += is_file($v) ? filesize($v) : self::get_dir_size($v);
        }
        return $s;
    }

    /**
     * 只显示目录树
     * @param mixed $dirName 目录名
     * @param mixed $son 是否显示子目录
     * @param mixed $pid 父目录ID
     * @param mixed $dirs 目录列表
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    static public function treeDir($dirName = null, $son = 0, $pid = 0, $dirs = array())
    {
        if (!$dirName)
            $dirName = '.';
        static $id = 0;
        $dirPath = self::dirPath($dirName);
        foreach (glob($dirPath . "*") as $v) {
            if (is_dir($v)) {
                $id++;
                $dirs[$id] = array("id" => $id, 'pid' => $pid, "dirname" => basename($v), "dirpath" => $v);
                if ($son) {
                    $dirs = self::treeDir($v, $son, $id, $dirs);
                }
            }
        }
        return $dirs;
    }

    /**
     * 删除目录及文件，支持多层删除目录
     * @param mixed $dir 需要删除的目录
     * @param mixed $ignorePatterns 需要忽略的文件或目录
     * @throws \Exception
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function delDir($dir, $ignorePatterns = [])
    {
        try {
            if (!is_dir($dir)) {
                throw new Exception('该目录不存在');
            }
            $files = glob("{$dir}/{,.}*", GLOB_BRACE | GLOB_NOSORT);
            $files = array_filter($files, function ($file) {
                return !in_array(basename($file), ['.', '..']);
            });
            foreach ($files as $pathName) {
                if (!in_array(rtrim($pathName, '/'), $ignorePatterns)) {
                    if (is_dir($pathName)) {
                        self::delDir($pathName, $ignorePatterns);
                        # 判断是否为空目录，删除空目录
                        if (self::isDirEmpty($pathName)) {
                            @rmdir($pathName);
                        }
                    } else if (file_exists($pathName)) {
                        @unlink($pathName);
                    }
                }
            }
            # 判断是否为空目录，删除空目录
            if (self::isDirEmpty($dir)) {
                @rmdir($dir);
            }
        } catch (\Throwable $e) {
            Log::error("删除目录出错：{$e->getMessage()}，line：{$e->getLine()}，file：{$e->getFile()}");
            throw $e;
        }
    }

    /**
     * 检测目录是否为空
     * @param mixed $dir
     * @return bool|null
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function isDirEmpty($dir)
    {
        if (!is_readable($dir))
            return null;
        return (count(glob("$dir/*", GLOB_BRACE | GLOB_NOSORT)) === 0);
    }
}