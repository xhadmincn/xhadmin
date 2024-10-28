<?php

namespace app\controller;

use app\common\BaseController;

class IndexController extends BaseController
{
    public function index()
    {
        # 检测未安装
        if (!file_exists(root_path().'.env')) {
            return redirect('/install/');
        }
        return redirect('/store/');
    }
}
