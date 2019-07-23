<?php
namespace app\index\controller;

class Index extends Common
{
    public function index()
    {
    	$articleM=new \app\index\model\Article();
    	$newArtiecleRes=$articleM->getNewArticle();//获取最新5篇文章调用
        $siteHotArt=$articleM->getSiteHot();//获取全站的5篇热点文章
        $recArt=$articleM->getRecArt();//获取所有的推荐文章【最多获取4篇】 在首页轮播图中显示
        //获取推荐栏目【底部的在common中做了 因为底部推荐所有的页面都需要】
        $cateM=new \app\index\model\Cate();
        $recIndex=$cateM->getRecIndex();//获取被推荐到顶部的栏目

        $linkRes=db('link')->order('sort desc')->select();//友情链接数据
    	$this->assign(array(
    		'newArtiecleRes'=>$newArtiecleRes,
            'siteHotArt'=>$siteHotArt,
            'linkRes'=>$linkRes,
            'recArt'=>$recArt,
            'recIndex'=>$recIndex,
    		));
        return view();
    }
}
