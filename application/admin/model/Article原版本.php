<?php

namespace app\admin\model;

use think\Model;

class Article extends Model
{

    protected static function init()
    {
        //没有对上传缩略图的大小等进行相关的设置
        //当添加save的时候触发
        //$article是控制器中save中的参数 下面的同理
        Article::event('before_insert', function ($article) {
            if ($_FILES['thumb']['tmp_name']) {//输出文件名，判断是否进行了上传
                $file = request()->file('thumb');
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');//本机上打印出来的ROOT_PATH路径 D:\PHPWAMP_IN3_1\wwwroot\tp5_2
                if ($info) {
                    $thumb = DS . 'uploads' . DS . $info->getSaveName();
                    $article['thumb'] = $thumb;//保存到数据库中的相应字段中
                }
            }
        });

        //当更新update的时候触发
        Article::event('before_update', function ($article) {
            if ($_FILES['thumb']['tmp_name']) {//输出文件名，判断是否进行了上传【如果edit中没有上传缩略图 是不会进行下面的步骤的】

                //更新时判断原来有没有缩略图，有的话就先进行删除 --开始
                $arts = Article::find($article->id);
                //$_SERVER['DOCUMENT_ROOT']为根目录D:/PHPWAMP_IN3_1/wwwroot/tp5_2/public
                $thumbpath = $_SERVER['DOCUMENT_ROOT'] . $arts['thumb'];
                if (file_exists($thumbpath)) {//file_exists用这个判断文件是否存在比直接用$thumbpath好 file_exists貌似不支持www.127.0[域名形式]这种形式的判断
                    @unlink($thumbpath);//删除原先的缩略图   http不允许unliking【断开连接】 unlink貌似不能对127.0.0.1[域名形式]这种路径进行删除，和file_exists类似
                }
                //更新时判断原来有没有缩略图，有的话就先进行删除  --结束

                //下面和添加的代码一样
                $file = request()->file('thumb');
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');//移动文件的位置，不能采用下面的那种
//              $info = $file->move('http:' . DS . '' . DS . '127.0.0.1' . DS . 'tp5_2' . DS . 'public' . DS . 'uploads');
                if ($info) {
                    $thumb = DS . 'uploads' . DS . $info->getSaveName();
                    $article['thumb'] = $thumb;
                }

            }
        });

        //当删除delete的时候触发  删除对应服务器上的图片
        Article::event('before_delete', function ($article) {
            $arts = Article::find($article->id);
            $thumbpath = $_SERVER['DOCUMENT_ROOT'] . $arts['thumb'];
            if (file_exists($thumbpath)) {
                @unlink($thumbpath);
            }
        });

    }
}
