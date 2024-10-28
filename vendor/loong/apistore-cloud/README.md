# 壹定开放平台SDK

#### 安装

```
composer require loong/apistore-cloud
```

#### 使用说明
``` php
<?php

require '../vendor/autoload.php';

use loong\ApiStore\facade\Http;

echo "<pre>";
try {
    $ret = Http::setToken('')
        ->post('driveSearch/chat/Local/execute', [
            'text' => '下午3：00HDHD99<返回学法减分考试?1234567891011121314151617181920剩余58S(单选题)18、如图所示，A车右转遇人行横道有行人通过，以下做法正确的是什么？A:保持较低车速通过B:停车让行，等行人通过后再通过C:确保安全的前提下绕行通过D:连续鸣喇叭冲开人群A下一题三<'
        ])->response();
    print_r($ret->toArray());
} catch (\Throwable $th) {
    echo $th->getMessage();
}
echo "</pre>";
```