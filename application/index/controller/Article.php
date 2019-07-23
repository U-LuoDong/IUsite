<?php
namespace app\index\controller;
use think\Request;
class Article extends Common
{
    public function index()
    {
    	$artid=input('artid');
    	db('article')->where(array('id'=>$artid))->setInc('click',1);//setInc【设置浏览数自增1】
    	$articles=db('article')->find($artid);
    	$article= new \app\index\model\Article();
    	$hotRes=$article->getHotRes($articles['cateid']);//获取该栏目下的热点文章
    	$this->assign(array(
    		'articles'=>$articles,
    		'hotRes'=>$hotRes,
            'artid'=>$artid,
    		));
        return view('article');
    }

}
