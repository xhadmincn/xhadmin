<?php

return [
    // 静态文件后缀
    'static_suffix' => ['html', 'xml', 'json', 'css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'ico', 'woff', 'woff2', 'ttf', 'eot', 'svg', 'map', 'vue','svg','doc','docx','xls','xlsx','ppt','pptx','pdf','txt','md','mp4','mp3','avi','rmvb','rm','wmv','swf','flv','exe','apk','zip','rar','7z','gz','tar','iso','dmg','csv','tsv','wasm','sql','psd','ai','sketch','fig','eps','ttc','otf','dmg','iso','img','bin','dat','dll','sys','tmp','log','torrent','torrent','torrent','torrent','torrent','torrent','torrent','tor','webp','mov'],
    // 中间件
    'middleware' => [
        \app\common\middleware\XhMiddleware::class,
    ]
];