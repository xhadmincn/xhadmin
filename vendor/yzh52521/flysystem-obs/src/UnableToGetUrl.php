<?php
declare( strict_types = 1 );

namespace yzh52521\Flysystem\Obs;

use League\Flysystem\FilesystemException;
use RuntimeException;

class UnableToGetUrl extends RuntimeException implements FilesystemException
{
    public static function missingOption(string $option): self
    {
        return new self( sprintf( 'Unable to get url with option %s missing.',$option ) );
    }
}