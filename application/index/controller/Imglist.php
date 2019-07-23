<?php
namespace app\index\controller;
use app\index\model\Article;
class Imglist extends Common
{
    public function index()
    {
    	$article=new Article();
    	$artRes=$article->getAllArticles(input('cateid'));//获取栏目对应的图片列表
    	$cate=new \app\index\model\Cate();
        $cateInfo=$cate->getCateInfo(input('cateid'));//获取某个栏目的信息，前端SEO优化显示
    	$this->assign(array(
    		'artRes'=>$artRes,
    		'cateInfo'=>$cateInfo,
    		));
        return view('imglist');
    }

    /**
     * 为了适应手机壁纸而设置的页面
     * @return \think\response\View
     */
    public function indexPhone()
    {
        $article=new Article();
        $artRes=$article->getAllArticles(input('cateid'));//获取栏目对应的图片列表
        $cate=new \app\index\model\Cate();
        $cateInfo=$cate->getCateInfo(input('cateid'));//获取某个栏目的信息，前端SEO优化显示
        $this->assign(array(
            'artRes'=>$artRes,
            'cateInfo'=>$cateInfo,
        ));
        return view('index_phone');
    }
    public function download(){
        header('Content-type: image/png');//网页已是默认的图片格式  不能数输出其他的格式 否则会有乱码
        header('Content-Disposition: attachment; filename='.time().'IU.png');//将文件设置为附件格式(浏览器只会下载而不会打开附件格式)，设置下载时显示的文件名
        $artId=input('artid');
        $lj=db('article')->field('thumb')->where('id','=',$artId)->select();
        readfile('D:\PHPWAMP_IN3_1\wwwroot\tp5_2\public'.$lj[0]['thumb']);//读取文件并写入到输出缓冲
    }
}
