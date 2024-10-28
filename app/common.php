<?php

use app\common\manager\StoreAppMgr;
use app\common\service\UploadService;
use phpseclib3\Math\BigInteger\Engines\PHP;
use support\Response;

/**
 * 获取映射模块名称
 * @param string $moduleName
 * @return string
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-04-30
 */
function getModule(string $moduleName): string
{
    $modules = config('admin');
    if (isset($modules[$moduleName])) {
        $moduleName = $modules[$moduleName];
    }
    return $moduleName;
}

/**
 * 验证器（支持场景验证）
 * @param mixed $validate
 * @param array $data
 * @param string $scene
 * @throws Exception
 * @return bool
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-04-30
 */
function hpValidate($validate, array $data, string $scene = ''): bool
{
    // 实例类
    $class = new $validate;
    // 场景验证
    if ($scene) {
        $class->scene($scene);
    }
    $result = $class->check($data);
    if (!$result) {
        throw new Exception((string) $class->getError(),404);
    }
    return true;
}

/**
 * 获取后台视图
 * @param mixed $plugin
 * @throws \Exception
 * @return think\Response
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
function getAdminView($plugin = '')
{
    $viewPath = public_path() . 'xhadmin/index.html';
    if (!file_exists($viewPath)) {
        throw new Exception('官方后台视图模板文件不存在');
    }
    $content  = file_get_contents($viewPath);
    $response = Response::create()->content($content);
    return $response;
}

/**
 * 获取配置项
 * @param string|array $key
 * @param mixed $appid
 * @param string $group
 * @return mixed
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
function getHpConfig(string|array $fields = '', $appid = null, string $group = '')
{
    $model = new \app\common\model\SystemConfig;
    if ($appid) {
        # 应用级配置
        $model = $model->where('saas_appid', $appid);
    } else {
        # 系统级配置
        $model = $model->where('store_id', NULL);
        $model = $model->where('saas_appid', NULL);
    }
    # 读取分组
    if ($group) {
        $model = $model->where('group', $group);
    }
    $data = $model->field('value')->column('value');
    # 取出部分数据
    if ($fields && is_array($fields)) {
        $list = [];
        foreach ($data as $value) {
            $item = json_decode($value, true);
            if (is_array($item)) {
                foreach ($item as $name => $rowValue) {
                    if (is_array($fields) && in_array($name, $fields)) {
                        $list[$name] = $rowValue;
                    }
                }
            }
        }
        return $list;
    }
    # 组装所需数据
    $list = [];
    foreach ($data as $value) {
        $item = json_decode($value, true);
        if (is_array($item)) {
            foreach ($item as $name => $rowValue) {
                $list[$name] = $rowValue;
            }
        }
    }
    # 字符串处理
    if ($fields && is_string($fields)) {
        # 取出部分数据
        return $list[$fields] ?? '';
    }
    # 取出全部数据
    if (empty($fields) && is_string($fields)) {
        return $list;
    }
    # 取出失败
    return null;
}

/**
 * 获取管理员数据
 * @param string $key
 * @return mixed
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
function hp_admin(string $key = '')
{
    $data = \think\facade\Session::get($key);
    return $data ?? [];
}

/**
 * 获取管理员ID
 * @param mixed $key
 * @return mixed
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
function hp_admin_id(string $key = '')
{
    $data = hp_admin($key);
    return isset($data['id']) ? $data['id'] : 0;
}

/**
 * 友好时间显示
 * @param int $time
 * @return bool|string
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 */
function friend_date(int $time)
{
    if (!$time)
        return false;
    $fdate = '';
    $d     = time() - intval($time);
    $ld    = $time - mktime(0, 0, 0, 0, 0, date('Y')); //得出年
    $md    = $time - mktime(0, 0, 0, date('m'), 0, date('Y')); //得出月
    $byd   = $time - mktime(0, 0, 0, date('m'), date('d') - 2, date('Y')); //前天
    $yd    = $time - mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')); //昨天
    $dd    = $time - mktime(0, 0, 0, date('m'), date('d'), date('Y')); //今天
    $td    = $time - mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')); //明天
    $atd   = $time - mktime(0, 0, 0, date('m'), date('d') + 2, date('Y')); //后天
    if ($d == 0) {
        $fdate = '刚刚';
    } else {
        switch ($d) {
            case $d < $atd:
                $fdate = date('Y年m月d日', $time);
                break;
            case $d < $td:
                $fdate = '后天' . date('H:i', $time);
                break;
            case $d < 0:
                $fdate = '明天' . date('H:i', $time);
                break;
            case $d < 60:
                $fdate = $d . '秒前';
                break;
            case $d < 3600:
                $fdate = floor($d / 60) . '分钟前';
                break;
            case $d < $dd:
                $fdate = floor($d / 3600) . '小时前';
                break;
            case $d < $yd:
                $fdate = '昨天' . date('H:i', $time);
                break;
            case $d < $byd:
                $fdate = '前天' . date('H:i', $time);
                break;
            case $d < $md:
                $fdate = date('m月d日 H:i', $time);
                break;
            case $d < $ld:
                $fdate = date('m月d日 H:i', $time);
                break;
            default:
                $fdate = date('Y年m月d日', $time);
                break;
        }
    }
    return $fdate;
}
/**
 * 生成6位随机数
 * @param int $len
 * @return string
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 */
function get_random(int $len = 6)
{
    $unique_no = substr(base_convert(md5(uniqid(md5(microtime(true)), true)), 16, 10), 0, $len);
    return $unique_no;
}
/**
 * XML转数组
 *
 * @Author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-12
 * @param  string $xml
 * @return array
 */
function xmlToArr(string $xml): array
{
    //将xml转化为json格式
    $jsonxml = json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA));
    //转成数组
    $result = json_decode($jsonxml, true);

    // 返回数组
    return $result;
}

/**
 * 根据大小返回标准单位 KB  MB GB等
 *
 * @Author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-12
 * @param  integer $size
 * @param  integer $decimals
 * @return string
 */
function get_size(int $size, int $decimals = 2): string
{
    switch (true) {
        case $size >= pow(1024, 3):
            return round($size / pow(1024, 3), $decimals) . " GB";
        case $size >= pow(1024, 2):
            return round($size / pow(1024, 2), $decimals) . " MB";
        case $size >= pow(1024, 1):
            return round($size / pow(1024, 1), $decimals) . " KB";
        default:
            return $size . 'B';
    }
}
/**
 * 下划线转驼峰
 *
 * step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
 * step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
 * @param string $uncamelized_words
 * @param string $separator
 * @return string
 */
function camelize(string $uncamelized_words, $separator = '_'): string
{
    $uncamelized_words = $separator . str_replace($separator, " ", strtolower($uncamelized_words));
    return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator);
}
/**
 * 驼峰命名转下划线命名
 *
 * 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
 * @param string $camelCaps
 * @param string $separator
 * @return string
 */
function uncamelize(string $camelCaps, $separator = '_'): string
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
}

/**
 * 对查询结果集进行排序
 * asc正向排序 desc逆向排序 nat自然排序
 * @param mixed $list 查询结果
 * @param mixed $field 排序的字段名
 * @param mixed $sortby 排序类型
 * @return array|bool
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-05-03
 */
function list_sort_by(array $list, string $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}

/**
 * 输出日志到终端（仅调试模式下有效）
 * @param mixed $str
 * @param string $remarks
 * @param bool $type
 * @return void
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-04-29
 */
function p($str, string $remarks = '日志：', bool $type = false)
{
    echo "<pre>";
    echo $remarks.PHP_EOL;
    print_r($str);
    echo "</pre>";
}

/**
 * 获取授权内容
 *
 * @param string $fileName
 * @param string $default
 * @return mixed
 */
function empowerFile(string $fileName, $default = '')
{
    $path = root_path() . "/{$fileName}.pem";
    if (!file_exists($path)) {
        return $default;
    }
    $content = file_get_contents($path);
    if (!$content) {
        return $default;
    }
    return $content;
}

/**
 * 渲染自定义页面视图
 * @param string $file
 * @param mixed $plugin
 * @param string $suffix
 * @return bool|string
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 */
function renderCustomView(string $file, $plugin = '', string $suffix = 'vue')
{
    # 根目录官方视图文件
    $filePath = public_path() . $file . "." . $suffix;
    if ($plugin) {
        $filePath = root_path("plugin/{$plugin}/public/");
    }
    if (!file_exists($filePath)) {
        throw new Exception('视图文件不存在');
    }
    $content = file_get_contents($filePath);
    return $content;
}