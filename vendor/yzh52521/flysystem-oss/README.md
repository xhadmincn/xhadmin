## Aliyun OSS Adapter For Flysystem.

AliYun OSS Storage adapter for flysystem - a PHP filesystem 3.0 abstraction.

# Requirement

- PHP >= 8.0

## Installation
composer require yzh52521/flysystem-oss 3.0



## Usage

```php
use League\Flysystem\Filesystem;
use yzh52521\Flysystem\Oss\OssAdapter;

$config = [
    "access_id" => "**************",             // Required, YourAccessKeyId
    "access_secret" => "********************",   // Required, YourAccessKeySecret
    "endpoint" => "oss-cn-shanghai.aliyuncs.com",// Required, Endpoint
    "bucket" => "bucket-name",                   // Required, Bucket
    "prefix" => "",
    'isCName'=> true, // 如果 isCname 为 false，endpoint 应配置 oss 提供的域名如：`oss-cn-beijing.aliyuncs.com`，cname 或 cdn 请自行到阿里 oss 后台配置并绑定 bucket
    'cndUrl' =>''
    "options"=> [
       
    ]
];

$adapter = new OssAdapter($config);

// $adapter->setCdnUrl('https://cdn.xxx.com'); // 设置cdn url

$flysystem = new Filesystem($adapter);


$flysystem->write('file.md', 'contents');
$flysystem->writeStream('foo.md', fopen('file.md', 'r'));

$fileExists = $flysystem->fileExists('foo.md');
$flysystem->copy('foo.md', 'baz.md');
$flysystem->move('baz.md', 'bar.md');
$flysystem->delete('bar.md');
$has = $flysystem->has('bar.md');

$read = $flysystem->read('file.md');
$readStream = $flysystem->readStream('file.md');

$flysystem->createDirectory('foo/');
$directoryExists = $flysystem->directoryExists('foo/');
$flysystem->deleteDirectory('foo/');

$listContents = $flysystem->listContents('/', true);
$listPaths = [];
foreach ($listContents as $listContent) {
    $listPaths[] = $listContent->path();
}

$lastModified = $flysystem->lastModified('file.md');
$fileSize = $flysystem->fileSize('file.md');
$mimeType = $flysystem->mimeType('file.md');

$flysystem->setVisibility('file.md', 'private');
$visibility = $flysystem->visibility('file.md');

string $flysystem->getUrl('file.md'); 

```


