<?php
namespace app\index\model;
use think\Model;
class Cate  extends Model
{
    //和后台中的cate类似

    /**
     * 查找子集栏目
     * @param $cateid
     * @return string
     */
    public function getchilrenid($cateid){
        $cateres=$this->select();
        $arr=$this->_getchilrenid($cateres,$cateid);
        $arr[]=$cateid;//还要加上自己的id
        $strId=implode(',', $arr);//将数组转换成一个字符串
        return $strId;
    }

    public function _getchilrenid($cateres,$cateid){
        static $arr=array();
        foreach ($cateres as $k => $v) {
            if($v['pid'] == $cateid){
                $arr[]=$v['id'];
                $this->_getchilrenid($cateres,$v['id']);
            }
        }
        return $arr;
    }

    /**
     * 查找父级栏目
     * @param $cateid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getparents($cateid){
        $cateres=$this->field('id,pid,catename')->select();
        $cates=db('cate')->field('id,pid,catename')->find($cateid);
        $pid=$cates['pid'];
        if($pid){//如果pid为0的话为顶级栏目就不需要找了
            $arr=$this->_getparentsid($cateres,$pid);
        }
        $arr[]=$cates;
        return $arr;
    }

    public function _getparentsid($cateres,$pid){
        static $arr=array();
        foreach ($cateres as $k => $v) {
            if($v['id'] == $pid){
                $arr[]=$v;
                $this->_getparentsid($cateres,$v['pid']);
            }
        }
        return $arr;
    }

    /**
     * 获取被推荐到顶部的栏目
     * @return mixed
     */
    public function getRecIndex(){
        $recIndex=$this->order('id desc')->where('rec_index','=',1)->select();
        return $recIndex;
    }

    /**
     * 获取被推荐到底部的栏目
     * @return mixed
     */
    public function getRecBottom(){
        $RecBottom=$this->order('id desc')->where('rec_bottom','=',1)->select();
        return $RecBottom;
    }

    /**
     * 获取栏目信息
     * @param $cateid
     * @return mixed
     */
    public function getCateInfo($cateid){
        $cateInfo=$this->field('catename,keywords,desc')->find($cateid);
        return $cateInfo;
    }


}
