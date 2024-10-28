<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 附件类型
 * 支持如下 images,image,files,file
 *
 * @author 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-06
 */
class UploadifyType extends Enum
{
    const IMAGES = [
        'text'      => '多图',
        'value'     => 'image'
    ];
    const IMAGE = [
        'text'      => '单图',
        'value'     => 'image'
    ];
    const FILES = [
        'text'      => '多文件',
        'value'     => 'files'
    ];
    const FILE = [
        'text'      => '单文件',
        'value'     => 'file'
    ];
}
