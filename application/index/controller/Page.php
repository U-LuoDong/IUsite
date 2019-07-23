<?php
namespace app\index\controller;

class Page extends Common
{
    public function index()
    {
    	$cates=db('cate')->find(input('cateid'));
    	$cate=new \app\index\model\Cate();
        $cateInfo=$cate->getCateInfo(input('cateid'));//获取某个栏目的信息，前端SEO优化显示
    	$this->assign(array(
    		'cates'=>$cates,
    		'cateInfo'=>$cateInfo,
    		));
        return view('page');
    }
}
