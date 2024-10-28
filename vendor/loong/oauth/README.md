# 接口授权验证

#### 安装

```
composer require loong/oauth
```

#### 使用说明
``` php
<?php
require 'vendor/autoload.php';

use loong\oauth\exception\SingleException;
use loong\oauth\exception\TokenExpireException;
use loong\oauth\facade\Auth;
# data可被json序列化的数据
$data = [
    'id' => 1
];
# 加密
$token = Auth::setExpire(2)->encrypt($data);
var_dump($token);
try {
    # 解密
    $data = Auth::decrypt($token);
    var_dump($data);
} catch (TokenExpireException $e) {
    # token已过期
    var_dump($e->getMessage());
} catch (SingleException $e) {
    # 单点登录被踢下线
    var_dump($e->getMessage());
} catch (\Throwable $th) {
    var_dump($th->getMessage());
}
```

#### 使用说明

需要实现全局方法:`config_path()`，返回绝对路径