<?php
declare(strict_types=1);
use think\helper\{
    Str, Arr
};
if (!function_exists('getPluginName')) {
    /**
     * 获取当前请求的插件名称
     * @return string|null
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    function getPluginName()
    {
        $request  = request();
        $pathInfo = $request->pathinfo();
        // 检测字符串中是否带有app/字符串
        if (strpos($pathInfo, 'app/') === false) {
            return '';
        }
        $response = explode('/',$pathInfo);
        if (count($response) < 2) {
            return '';
        }
        return isset($response[1]) ? $response[1] : '';
    }
}
if (!function_exists('getPluginConfig')) {
    /**
     * 获取插件配置
     * @param mixed $name
     * @param mixed $default
     * @return array
     * @author John
     */
    function getPluginConfig($name,$default = null)
    {
        $pluginName = getPluginName();
        if (empty($pluginName)) {
            return $default;
        }
        $pluginPath = getPluginPath($pluginName, '');
        if (empty($pluginPath)) {
            return $default;
        }
        $configPath = "{$pluginPath}/config";
        // 加载配置
        \think\facade\Config::load("{$configPath}/{$name}.php",$name);
        // 读取配置
        $config = config($name,$default);
        return $config;
    }
}
if (!function_exists('getPluginPath')) {
    /**
     * 获取插件路径
     * @param mixed $name
     * @param mixed $default
     * @return mixed
     * @author John
     */
    function getPluginPath($name,$default = null)
    {
        $app = app();
        $rootPath = $app->getRootPath();
        $pluginPath = "{$rootPath}plugin/{$name}/";
        if (!is_dir($pluginPath)) {
            return $default;
        }
        return $pluginPath;
    }
}
if (!function_exists('getPluginInfo')) {
    /**
     * 获取插件信息
     * @param mixed $name
     * @param mixed $default
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    function getPluginInfo($name,$default = null)
    {
        $pluginPath = getPluginPath($name);
        if (empty($pluginPath)) {
            return $default;
        }
        $infoFile = "{$pluginPath}/info.json";
        if (!is_file($infoFile)) {
            return $default;
        }
        $info = file_get_contents($infoFile);
        $info = json_decode($info,true);
        return $info;
    }
}
if (!function_exists('getPluginControl')) {
    /**
     * 获取插件类的类命名空间
     * @param mixed $name
     * @param mixed $class
     * @param mixed $suffix
     * @return string
     * @author John
     */
    function getPluginControl($pluginName, $class = null,$suffix = '')
    {
        $name = trim($pluginName);
        // 处理多级控制器情况
        if (!is_null($class) && strpos($class, '/')) {
            $class = explode('/', $class);

            $class[count($class) - 1] = Str::studly(end($class));
            $class = implode('\\', $class);
        } else {
            $class = Str::studly(is_null($class) ? $name : $class);
            $class = "controller\\{$class}";
        }
        $namespace = "\\plugin\\{$name}\\app\\{$class}{$suffix}";
        return $namespace;
    }
}
if (!function_exists('getBetween')) {
    /**
     * 截取指定两个字符之间内容
     * @param mixed $input
     * @param mixed $start
     * @param mixed $end
     * @return string
     * @author John
     */
    function getBetween($input, $start, $end)
    {
        $substr = substr($input, strlen($start) + strpos($input, $start), (strlen($input) - strpos($input, $end)) * (-1));
        return $substr;
    }
}
if (!function_exists('getAssetsCheck')) {
    /**
     * 检测是否存在静态资源并处理
     * @param support\Request $request
     * @return bool|think\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    function getAssetsCheck(\think\Request $request)
    {
        $staticSuffix = config('plugins.static_suffix');
        if (!is_array($staticSuffix)) {
            throw new Exception("配置项plugins.static_suffix必须为数组");
        }
        if (empty($staticSuffix)) {
            throw new Exception("配置项plugins.static_suffix不能为空");
        }
        # 检测是否资源文件
        $extension = pathinfo($request->pathinfo(), PATHINFO_EXTENSION);
        if (in_array($extension, $staticSuffix)) {
            $mimeContentTypes = [
                'xml'   => 'application/xml,text/xml,application/x-xml',
                'json'  => 'application/json,text/x-json,application/jsonrequest,text/json',
                'js'    => 'text/javascript,application/javascript,application/x-javascript',
                'css'   => 'text/css',
                'rss'   => 'application/rss+xml',
                'yaml'  => 'application/x-yaml,text/yaml',
                'atom'  => 'application/atom+xml',
                'pdf'   => 'application/pdf',
                'text'  => 'text/plain',
                'image' => 'image/png,image/jpg,image/jpeg,image/pjpeg,image/gif,image/webp,image/*',
                'csv'   => 'text/csv',
                'html'  => 'text/html,application/xhtml+xml,*/*',
                'vue'   => 'application/octet-stream',
                'svg'   => 'image/svg+xml',
            ];
            # 检测文件媒体类型
            $mimeContentType = 'text/plain';
            if (isset($mimeContentTypes[$extension])) {
                $mimeContentType = $mimeContentTypes[$extension];
            }
            # 检测是否框架GZ资源
            $file = public_path().$request->pathinfo();
            if (file_exists($file)) {
                $content  = file_get_contents($file);
                return response()->code(200)->contentType($mimeContentType)->content($content);
            }
            # 检测是否插件资源
            $pluginRoute = explode('/',$request->pathinfo());
            if (isset($pluginRoute[1])) {
                $plugin = $pluginRoute[1];
                unset($pluginRoute[0]);
                unset($pluginRoute[1]);
                $pluginRoute = implode('/', $pluginRoute);
                $file = root_path()."plugin/{$plugin}/public/{$pluginRoute}";
                $fileGz=$file.'.gz';
                if(file_exists($fileGz)){
                    $content  = file_get_contents($fileGz);
                    $header=[
                        'Content-Type'      => $mimeContentType,
                        'Content-Encoding'  => 'gzip'
                    ];
                    return response()->code(200)->header($header)->content($content);
                }else if (file_exists($file)) {
                    $size= filesize($file);
                    $header=[
                        'Content-Type'      => $mimeContentType,
                    ];
                    $content  = file_get_contents($file);
                    if($size>1024*1024){
                        $header['Content-Encoding'] = 'gzip';
                        $content  = gzencode($content,9);
                    }
                    return response()->code(200)->header($header)->content($content);
                }
            }
            # 文件资源不存在则找官方库
            $file = root_path()."view/{$request->pathinfo()}.gz";
            if (file_exists($file)) {
                $content  = file_get_contents($file);
                return response()->code(200)->header([
                    'Content-Type'      => $mimeContentType,
                    'Content-Encoding'  => 'gzip'
                ])->content($content);
            }
            # 普通文件
            $file = root_path()."view/{$request->pathinfo()}";
            if (file_exists($file)) {
                $content  = file_get_contents($file);
                return response()->code(200)->contentType($mimeContentType)->content($content);
            }
        }
        return false;
    }
}
