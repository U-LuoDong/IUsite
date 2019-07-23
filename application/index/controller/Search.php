<?php
namespace app\index\controller;
use app\index\model\Article;
class Search extends Common
{
    /**
     * 搜索
     * @return \think\response\View
     */
    public function index()
    {
    	$article=new Article();
        $serHot=$article->getSerHot();//获取全站的热点文章【最多5篇】
        $keywords=input('keywords');
        $serRes=db('article')->order('id desc')->where('title','like','%'.$keywords.'%')->paginate(2,false,$config = ['query'=>array('keywords'=>$keywords)]);
        $this->assign(array(
            'serRes'=>$serRes,
            'keywords'=>$keywords,
            'serHot'=>$serHot,
            ));
//        if(empty($serRes[0])){
//            echo "空的";
//        }
//        dump(empty($serRes[0]));
//        dump($serRes);
//        die;
        return view('search');
    }
}
