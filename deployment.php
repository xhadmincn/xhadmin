<?php
namespace think;
use Exception;
use YcOpen\CloudService\Cloud;
use YcOpen\CloudService\Request\SystemUpdateRequest;

define('ROOT_PATH', __DIR__);
require ROOT_PATH . '/vendor/autoload.php';

// 执行HTTP应用并响应
$http = (new App())->http;

$response = $http->run();
$http->end($response);

header('Content-Type:application/json; charset=utf-8');

try {
    # 写入版本信息
    $req = new SystemUpdateRequest;
    $req->newVersion();
    $cloud             = new Cloud($req);
    $data              = $cloud->send();
    if (!isset($data->version)) {
        throw new Exception('获取最新版本失败');
    }
    $version = (int)$data->version;
    $versionData       = [
        'version'       => $version+1,
        'version_name'  => $data->version_name
    ];
    $versionJson       = json_encode($versionData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    file_put_contents(ROOT_PATH . '/config/version.json', $versionJson);
    exec("git add config/version.json");
    echo '最新版本自动部署成功！';
    echo PHP_EOL;
} catch (\Throwable $e) {
    exit($e->getMessage());
}