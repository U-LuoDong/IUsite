<?php
namespace app\index\controller;
use think\Controller;
class Common extends Controller
{

    public function _initialize(){
    	//当前位置
        //1、栏目
        if(input('cateid')){
            $this->getPos(input('cateid'));
        }
        //2、文章【根据文章id得到栏目id】
        if(input('artid')){
            $articles=db('article')->field('cateid')->find(input('artid'));
            $cateid=$articles['cateid'];
            $this->getPos($cateid);
        }

    	$this->getConf();//网站配置项【首页中有显示站点名称】

        $this->getNavCates();//网站栏目导航

        //获取被推荐到底部的栏目
        $cateM=new \app\index\model\Cate();
        $recBottom=$cateM->getRecBottom();
        $this->assign('recBottom',$recBottom);
    }


    /**
     * 网站栏目导航【但写死了 最多显示二级分类】
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNavCates(){
        $cateres=db('cate')->where(array('pid'=>0))->order('sort','desc')->select();//获取顶级分类的数组
        foreach ($cateres as $k => $v) {
            $children=db('cate')->where(array('pid'=>$v['id']))->select();//获取顶级分类的相应二级分类
            if($children){
                $cateres[$k]['children']=$children;//添加其对应二级分类字段，里面又是一个二维数组
            }else{
                $cateres[$k]['children']=0;
            }
        }
        $this->assign('cateres',$cateres);
    }

    public function getConf(){
        $conf=new \app\index\model\Conf();
        $_confres=$conf->getAllConf();//获取配置表中全部行的中英文字段
        $confres=array();
        foreach ($_confres as $k => $v) {
            $confres[$v['enname']]=$v['cnname'];//合并2个字段的内容 比如：SiteDesc=>站点描述
        }
        $this->assign('confres',$confres);
    }

    public function getPos($cateid){
        $cate= new \app\index\model\Cate();
        $posArr=$cate->getparents($cateid);//查找该栏目的父级栏目
        // dump($posArr); die;
        $this->assign('posArr',$posArr);
    }


}
