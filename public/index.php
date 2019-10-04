<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
//后台管理员头像缩略图位置
define('TXIMG', __DIR__ . '/../public/HeadPortrait/');
//文章缩略图位置
define('ARTICLEIMG', __DIR__ . '/../public/uploads/');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';

