<?php
namespace app\index\model;
use think\Model;
use app\index\model\Cate;
class Article  extends Model
{
    /**
     * 获取栏目id对应的文章
     * @param $cateid
     * @return mixed
     */
    public function getAllArticles($cateid){
        $cate=new Cate();
        $allCateID=$cate->getchilrenid($cateid);//获取该栏目的所有子栏目id
        $artRes=db('article')->where("cateid IN($allCateID)")->order('id desc')->paginate(6);
        return $artRes;
    }

    /**
     * 获取对应栏目的热门文章【按点击数进行排序 最多5篇】
     * @param $cateid
     * @return mixed
     */
    public function getHotRes($cateid){
        $cate=new Cate();
        $allCateID=$cate->getchilrenid($cateid);
        $artRes=db('article')->where("cateid IN($allCateID)")->order('click desc')->limit(5)->select();
        return $artRes;
    }

    /**
     * 获取全站的热点文章  只查找文章和单页栏目的
     * @return mixed
     */
    public function getSerHot(){
        $artId=db('cate')->field('id')->where("type IN(1,2)")->select();
        $arr=array();
        for ($i=0;$i<count($artId);$i++){
            $arr[]=$artId[$i]['id'];
        }
        $articalId=implode(',', $arr);//将数组转换成一个字符串
       $artRes=db('article')->where("cateid IN($articalId)")->order('click desc')->limit(5)->select();
        return $artRes; 
    }

    /**
     * 获取全站的热点文章   只查找文章和单页栏目的
     * @return mixed
     */
    public function getSiteHot(){
        $artId=db('cate')->field('id')->where("type IN(1,2)")->select();
        $arr=array();
        for ($i=0;$i<count($artId);$i++){
            $arr[]=$artId[$i]['id'];
        }
        $articalId=implode(',', $arr);//将数组转换成一个字符串
        $siteHotArt=$this->field('id,title,thumb')->where("cateid IN($articalId)")->order('click desc')->limit(5)->select();
        return $siteHotArt;
    }

    /**
     * 获取最新的文章【连表】 只查找文章和单页栏目的
     * @return mixed
     */
    public function getNewArticle(){
        $artId=db('cate')->field('id')->where("type IN(1,2)")->select();
        $arr=array();
        for ($i=0;$i<count($artId);$i++){
            $arr[]=$artId[$i]['id'];
        }
        $articalId=implode(',', $arr);//将数组转换成一个字符串
        $newArtiecleRes=db('article')->field('a.id,a.title,a.desc,a.thumb,a.click,a.zan,a.time,c.catename')->alias('a')->join('bk_cate c','a.cateid=c.id')->where("a.cateid IN($articalId)")->order('a.id desc')->limit(5)->select();
        return $newArtiecleRes;
    }

    /**
     * 获取所有的推荐文章【最多获取4篇】 在首页轮播图中显示
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRecArt(){
        $recArt=$this->where('rec','=',1)->field('id,title,thumb')->order('id desc')->limit(4)->select();
        return $recArt;
    }
}
