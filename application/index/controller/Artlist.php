<?php
namespace app\index\controller;
use app\index\model\Article;
class Artlist extends Common
{
    public function index()
    {
    	$article=new Article();
        $cateid=input('cateid');//获取传递过来的栏目id
    	$artRes=$article->getAllArticles($cateid);//获取栏目对应的文章
    	$hotRes=$article->getHotRes($cateid);//获取热门文章【按点击数进行排序 最多5篇】
        $cate=new \app\index\model\Cate();
        $cateInfo=$cate->getCateInfo($cateid);//获取某个栏目的信息，前端SEO优化显示
        $this->assign(array(
            'artRes'=>$artRes,
    		'hotRes'=>$hotRes,
            'cateInfo'=>$cateInfo,
    		));
        return view('artlist');
    }
}
