<?php /*a:4:{s:76:"/www/wwwroot/saas.kf.renloong.com/plugin/ycDigitalHuman/public/pc/index.html";i:1728551271;s:83:"/www/wwwroot/saas.kf.renloong.com/plugin/ycDigitalHuman/public/pc/modules/main.html";i:1728551263;s:85:"/www/wwwroot/saas.kf.renloong.com/plugin/ycDigitalHuman/public/pc/modules/header.html";i:1728548862;s:83:"/www/wwwroot/saas.kf.renloong.com/plugin/ycDigitalHuman/public/pc/modules/menu.html";i:1728551217;}*/ ?>
<!DOCTYPE html>
<html lang="zh-cn" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=10,chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="maximum-scale=1.0, user-scalable=0">
    <meta name="aplus-xplug" content="NONE">
    <title><?php echo htmlentities($system['web_name']); ?></title>
    <link rel="icon" type="image/png" href="<?php echo htmlentities($system['web_logo']); ?>" />
    <meta name="keywords" content="<?php echo htmlentities($system['web_name']); ?>">
    <link rel="shortcut icon" href="<?php echo htmlentities($system['web_logo']); ?>">
    <link rel="apple-touch-icon" href="<?php echo htmlentities($system['web_logo']); ?>">
    <link rel="apple-touch-icon-precomposed" href="<?php echo htmlentities($system['web_logo']); ?>">
    <link rel="stylesheet" href="/app/ycDigitalHuman/pc/static/iconfont.css">
    <link rel="stylesheet" href="/app/ycDigitalHuman/pc/static/theme.min.css">
    <link rel="stylesheet" href="/app/ycDigitalHuman/pc/static/common.min.css">
    <link rel="stylesheet" href="/app/ycDigitalHuman/pc/static/index.css?t=<?php echo time(); ?>">
    <script>
        const __YCCONFIG = {
            domain: '<?php echo htmlentities($system['domain']); ?>',
            gt4: '<?php echo htmlentities($gt4_captcha_id); ?>',
        };
    </script>
</head>

<body>
    <div class="layouts">
        
            <div class="flex flex-center p-4">
    <a class="logo" href="<?php echo htmlentities($system['domain']); ?>/app/ycPaper?appid=<?php echo htmlentities($appid); ?>" alt="<?php echo htmlentities($system['web_name']); ?>">
        <img src="<?php echo htmlentities($system['web_logo']); ?>" class="logo-image" alt="<?php echo htmlentities($system['web_name']); ?>LOGO">
        <span class="logo-text"><?php echo htmlentities($system['web_name']); ?></span>
    </a>
    <div class="flex-1"></div>
    <div class="theme-switch">
        <label class="switch-label">
            <input type="checkbox" class="checkbox" id="themeSwitch"  checked/>
            <span class="slider"></span>
        </label>
    </div>
</div>
        
        <div class="flex flex-1 overflow-hidden grid-gap-4">
            
                <div class="menu p-4"></div>
            
            <div class="flex-1 overflow-y-scroll overflow-x-auto rounded-top-left-4">
                
<div style="height:200vh" class="bg-primary">
    dwad
</div>

            </div>
        </div>
    </div>
    <script src="/app/ycDigitalHuman/pc/static/qrcode.min.js"></script>
    <script src="/app/ycDigitalHuman/pc/static/axios.min.js"></script>
    <script>
        const appid = '<?php echo htmlentities($appid); ?>';
        axios.interceptors.request.use(function (config) {
            config.headers['Appid'] = appid;
            // 在发送请求之前做些什么
            return config;
        }, function (error) {
            // 对请求错误做些什么
            return Promise.reject(error);
        });
        axios.interceptors.response.use(function (response) {
            // 对响应数据做点什么
            return response.data;
        }, function (error) {
            // 对响应错误做点什么
            return Promise.reject(error);
        });
    </script>
    <script src="/app/ycDigitalHuman/pc/static/gt4.js"></script>
    <script src="/app/ycDigitalHuman/pc/static/loading.js"></script>
    <script src="/app/ycDigitalHuman/pc/static/message.js"></script>
    
<script src="/app/ycDigitalHuman/pc/static/index.js"></script>

</body>

</html>