<?php

namespace app\admin\model;

use think\Model;

class Article extends Model
{

    protected static function init()
    {
        //当删除delete的时候触发  删除对应服务器上的图片
        Article::event('before_delete', function ($article) {
            $arts = Article::find($article->id);
            $thumbpath = $_SERVER['DOCUMENT_ROOT']  .  $arts['thumb'];
            if (file_exists($thumbpath)) {
                @unlink($thumbpath);
            }
        });

    }
}
