# 客户端云服务中心

#### 安装

```
composer require yc-open/cloud-service
```

#### 使用说明
``` php
<?php
require 'vendor/autoload.php';
define('ROOT_PATH',dirname(__FILE__));
use YcOpen\CloudService\Request;
use YcOpen\CloudService\Exception\HttpException;
use YcOpen\CloudService\Exception\HttpResponseException;
use YcOpen\CloudService\Exception\ValidateException;
function p($data){
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}
function pe(\Throwable $th){
    p([
        'code'=>$th->getCode(),
        'message'=>$th->getMessage(),
        'file'=>$th->getFile(),
        'line'=>$th->getLine(),
        'trace'=>$th->getTrace(),
    ]);
}
function base_path($path=''){
    return ROOT_PATH.'/'.$path;
}
try {
    // Request::login()->outLogin();
    // Request::Login();
    /* $data=Request::coupon()->getAvailableCoupon()->setQuery(['type'=>'apps'])->cloud()->send();
    p($data); */
    $data=Request::SystemUpdate()->verify()->setQuery(['version'=>1,'version_name'=>'1.0.0'])->cloud()->send();
    p($data);
    return;
    /* $request = new CouponRequest;
    $request->getAvailableCoupon();
    $request->type='apps';
    $request->plugin_id=2;
    $cloud=new Cloud($request);
    $data=$cloud->send();
    p($data);
    return ;
    #获取验证码
    $request=new CaptchaRequest();
    $request->captchaCode();
    $cloud=new Cloud($request);
    $data=$cloud->send();
    p($data);

    #登录
    $request=new LoginRequest();
    $request->login();
    $request->username='18785305198';
    $request->password='123456';
    # 通过获取验证码接口获得token，如果不传token，会导致验证码验证失败
    // $request->token=$data->token;
    # no表示不需要验证码
    $request->scode='no';
    $cloud=new Cloud($request);
    $data=$cloud->send();
    p($data->token);

    #安装网站
    $request=new SiteRequest();
    $request->install();
    $request->ip=$_SERVER['SERVER_ADDR'];
    $request->domain=$_SERVER['HTTP_HOST'];
    $request->title='测试网站';
    $cloud=new Cloud($request);
    $data=$cloud->send(); 
    p($data);

    #获取用户信息
    $request=new UserRequest();
    $request->info();
    $cloud=new Cloud($request);
    $data=$cloud->send();
    p($data);

    #获取用户账单
    $request=new UserRequest();
    $request->getUserBill();
    $cloud=new Cloud($request);
    $data=$cloud->send(); 
    p($data->toArray());

    #获取插件列表
    $request=new PluginRequest();
    $request->list();
    $cloud=new Cloud($request);
    $data=$cloud->send(); 
    p($data);
    
    #获取插件详情
    $request=new PluginRequest();
    $request->detail();
    $request->name='ycSuperseo';
    $cloud=new Cloud($request);
    $data=$cloud->send(); 
    p($data);

    #购买插件
    $request=new PluginRequest();
    $request->buy();
    $request->name='ycSuperseo';
    $cloud=new Cloud($request);
    $data=$cloud->send(); 

    #获取插件下载密钥
    $request=new PluginRequest();
    $request->getKey();
    $request->name='ycSuperseo';
    $request->version=1;
    $request->saas_version=1;
    $request->local_version=1000;
    $cloud=new Cloud($request);
    $data=$cloud->send(); 
    p($data);

    #验证更新
    $request=new SystemUpdateRequest();
    $request->verify();
    $request->version_name='1.0.0';
    $request->version=1;
    $cloud=new Cloud($request);
    $data=$cloud->send(); 
    p($data);

    #下载安装包通用接口
    $request=new Request();
    # 通过获取下载密钥接口获得
    $request->setUrl($data->url);
    # 保存文件到指定路径
    $request->setSaveFile('/ycSuperseo.zip');
    $cloud=new Cloud($request);
    $data=$cloud->send(); 
    p($data); */
} catch (ValidateException $e) {
    # 参数验证错误
    pe($e);
} catch (HttpException $e) {
    # 服务器错误
    pe($e);
} catch (HttpResponseException $e) {
    # 业务错误
    pe($e);
} catch (\Throwable $th) {
    # 其他错误
    pe($th);
}
```