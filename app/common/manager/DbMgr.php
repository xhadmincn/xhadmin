<?php

namespace app\common\manager;

use Exception;
use think\facade\Db;

/**
 * Laravel数据库管理函数
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class DbMgr
{
    /**
     * 执行SQL文件语句
     * @param string $sql
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function querySQL(string $sql)
    {
        $sql = self::splitSqlFile($sql, ';');
        foreach ($sql as $item) {
            Db::statement($item);
        }
    }

    /**
     * 执行原生SQL
     * @param string $sql
     * @return bool
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function query(string $sql)
    {
        return Db::statement($sql);
    }

    /**
     * 检查表名是否合法
     * @param string $table
     * @throws \Exception
     * @return string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function checkTableName(string $table): string
    {
        if (!preg_match('/^[a-zA-Z_0-9]+$/', $table)) {
            throw new Exception('表名不合法');
        }
        return $table;
    }

    /**
     * 变量或数组中的元素只能是字母数字下划线组合
     * @param mixed $var
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function filterAlphaNum($var)
    {
        $vars = (array)$var;
        array_walk_recursive($vars, function ($item) {
            if (is_string($item) && !preg_match('/^[a-zA-Z_0-9]+$/', $item)) {
                throw new Exception('参数不合法');
            }
        });
        return $var;
    }

    /**
     * 去除sql文件中的注释
     * @param mixed $sql
     * @return string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function removeComments($sql): string
    {
        return preg_replace("/(\n--[^\n]*)/", "", $sql);
    }

    /**
     * 分割SQL文件
     * @param mixed $sql
     * @param mixed $delimiter
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function splitSqlFile($sql, $delimiter): array
    {
        $tokens = explode($delimiter, $sql);
        $output = array();
        $matches = array();
        $token_count = count($tokens);
        for ($i = 0; $i < $token_count; $i++) {
            if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0))) {
                $total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
                $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);
                $unescaped_quotes = $total_quotes - $escaped_quotes;

                if (($unescaped_quotes % 2) == 0) {
                    $output[] = $tokens[$i];
                    $tokens[$i] = "";
                } else {
                    $temp = $tokens[$i] . $delimiter;
                    $tokens[$i] = "";

                    $complete_stmt = false;
                    for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++) {
                        $total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
                        $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);
                        $unescaped_quotes = $total_quotes - $escaped_quotes;
                        if (($unescaped_quotes % 2) == 1) {
                            $output[] = $temp . $tokens[$j];
                            $tokens[$j] = "";
                            $temp = "";
                            $complete_stmt = true;
                            $i = $j;
                        } else {
                            $temp .= $tokens[$j] . $delimiter;
                            $tokens[$j] = "";
                        }
                    }
                }
            }
        }
        return $output;
    }

    /**
     * 获取连接实例
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function instance()
    {
        $connect = Db::connect();
        $connect->execute('show tables');
        return $connect;
    }
}
