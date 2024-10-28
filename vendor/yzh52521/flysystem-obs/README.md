# filesystem-obs

* PHP >= 8.0+
* League/Flysystem > 3.10+

## Installation

```
composer require yzh52521/flysystem-obs 
```

## Usage

```php
use League\Flysystem\Filesystem;
use Obs\ObsClient;
use yzh52521\Flysystem\Obs\ObsAdapter;

$prefix = '';
$config = [
    'key' => 'aW52YWxpZC1rZXk=',
    'secret' => 'aW52YWxpZC1zZWNyZXQ=',
    'bucket' => 'test',
    'endpoint' => 'obs.cn-east-3.myhuaweicloud.com',
];

$config['options'] = [
    'url' => '',
    'endpoint' => $config['endpoint'], 
    'bucket_endpoint' => '',
    'temporary_url' => '',
];

$client = new ObsClient($config);
$adapter = new ObsAdapter($client, $config['bucket'], $prefix, null, null, $config['options']);
$flysystem = new Filesystem($adapter);
```

## API

```php
bool $flysystem->write('file.md', 'contents');

bool $flysystem->write('file.md', 'http://httpbin.org/robots.txt', ['mime' => 'application/redirect302']);

bool $flysystem->writeStream('file.md', fopen('path/to/your/local/file.jpg', 'r'));

bool $flysystem->update('file.md', 'new contents');

bool $flysystem->updateStream('file.md', fopen('path/to/your/local/file.jpg', 'r'));

bool $flysystem->rename('foo.md', 'bar.md');

bool $flysystem->copy('foo.md', 'foo2.md');

bool $flysystem->delete('file.md');

bool $flysystem->has('file.md');

string|false $flysystem->read('file.md');

array $flysystem->listContents();

array $flysystem->getMetadata('file.md');

int $flysystem->getSize('file.md');

string $flysystem->getUrl('file.md'); 

string $flysystem->getMimetype('file.md');

int $flysystem->getTimestamp('file.md');
```

## License

MIT
