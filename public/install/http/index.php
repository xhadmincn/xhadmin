<?php
namespace think;

define('ROOT_PATH', dirname(dirname(dirname(__DIR__))));
define('XH_PUBLIC_PATH', ROOT_PATH.'/public');
define('XH_INSTALL_PATH', XH_PUBLIC_PATH.'/install');
define('XH_INSTALL_HTTP_PATH', XH_INSTALL_PATH.'/http');

require ROOT_PATH . '/vendor/autoload.php';

// 执行HTTP应用并响应
$http = (new App())->http;

$response = $http->run();
$http->end($response);

header('Content-Type:application/json; charset=utf-8');

try {
    // 引入外部类
    require XH_INSTALL_HTTP_PATH . "/utils/Validated.php";
    require XH_INSTALL_HTTP_PATH . "/utils/Json.php";
    require XH_INSTALL_HTTP_PATH . "/InstallUtil.php";
    require XH_INSTALL_HTTP_PATH.'/InstallController.php';

    $action = $_GET['a'] ?? 'index';
    $class  = "InstallController";
    $class   = new $class;
    exit($class->$action());
} catch (\Throwable $e) {
    exit(json_encode([
        'code'              => $e->getCode(),
        'msg'               => $e->getMessage(),
        'data'              => [
            'installed'     => true,
            'line'          => $e->getLine(),
            'file'          => $e->getFile(),
        ],
    ]));
}