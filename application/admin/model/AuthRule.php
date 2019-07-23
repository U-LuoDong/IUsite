<?php
namespace app\admin\model;
use think\Model;
class AuthRule extends Model
{
    
	public function authRuleTree(){
        $authRuleres=$this->order('sort asc')->select();//进行升序排序 但是下面sort明确规定了层级的顺序
        												//所以这里的排序实际上是对相同层级的排序顺序
        												//让其在相同层级中更快的被检索到
        return $this->sort($authRuleres);
    }

	/**
	 * $pid为父级的id  $pid=0表示为顶级权限【没有父级】
	 * 相对于栏目cate 表中有level字段，所以参数里面不用写level
	 */
    public function sort($data,$pid=0){
        static $arr=array();//静态数组：一直不会清空数组【所以递归的时候可以进行累加】
        foreach ($data as $k => $v) {//到底层权限的时候 就不会进行递归了【然后就一层层的进行返回】
            if($v['pid']==$pid){
            	$v['dataid']=$this->getparentid($v['id']);//并将该权限的父权限也放入这里面了【以字符串的形式】 实现配置权限中的勾选复选框
                $arr[]=$v;//将排序好的权限一直添加到$arr数组中去
                $this->sort($data,$v['id']);//寻找次一级的权限
            }
        }
        return $arr;
    }
    
	
	/**
	 * getchilrenid会在删除权限的时候用到【删除权限时要附带的删除它的子类】
	 */
    public function getchilrenid($authRuleId){
        $AuthRuleRes=$this->select();
        return $this->_getchilrenid($AuthRuleRes,$authRuleId);
    }
	
	/**
	 * 递归找出其所有的子类 和sort中的思想是一致的
	 */
    public function _getchilrenid($AuthRuleRes,$authRuleId){
        static $arr=array();
        foreach ($AuthRuleRes as $k => $v) {
            if($v['pid'] == $authRuleId){
                $arr[]=$v['id'];
                $this->_getchilrenid($AuthRuleRes,$v['id']);
            }
        }

        return $arr;
    }

	
	/**
	 * getparentid实现配置权限中的勾选复选框
	 */
    public function getparentid($authRuleId){//这里获取该权限的上级id  实现配置权限中的勾选复选框
        $AuthRuleRes=$this->select();
        return $this->_getparentid($AuthRuleRes,$authRuleId,True);
    }

    public function _getparentid($AuthRuleRes,$authRuleId,$clear=False){
        static $arr=array();//静态数组：一直不会清空数组  设置$clear=true的目的是清空数组【这里的arr应该和sort的arr不一样 作用域不同】
        if($clear){
        	$arr=array();
        }
        foreach ($AuthRuleRes as $k => $v) {
            if($v['id'] == $authRuleId){
                $arr[]=$v['id'];
                $this->_getparentid($AuthRuleRes,$v['pid'],False);
            }
        }
        asort($arr);
        $arrStr=implode('-', $arr);
        return $arrStr;
    }









}
