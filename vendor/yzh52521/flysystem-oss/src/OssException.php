<?php
namespace yzh52521\Flysystem\Oss;

use League\Flysystem\FilesystemException;
use RuntimeException;

class OssException extends RuntimeException implements FilesystemException
{

}