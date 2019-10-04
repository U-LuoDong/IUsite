<?php
namespace app\admin\controller;
use app\admin\model\AuthGroup as AuthGroupModel;
use app\admin\controller\Common;
class AuthGroup extends Common
{
    public function lst(){
        $authGroupRes=AuthGroupModel::paginate(2);
        $admins=db('admin')->find(session('id'));
        $this->assign(array(
            'admin'=>$admins,
            'authGroupRes'=>$authGroupRes,
        ));
        return view();
    }
    //异步修改用户组状态
    public function changeStatus()
    {
        $id = input('id');
        $authGroups = db('authGroup')->field('status')->find($id);
        if ($authGroups['status'] == 1) {
            db('authGroup')->where(array('id' => $id))->update(['status' => 0]);
        } else {
            db('authGroup')->where(array('id' => $id))->update(['status' => 1]);
        }
    }

    public function add(){
        if(request()->isPost()){
            $data=input('post.');
       	//针对复选框的操作
           	//1、针对管理用户组的权限选择开始
            $_data=array();
            foreach ($data as $k => $v) {
                $_data[]=$k;//将页面提交的字段组合成一个一维数组
            }
//          if($data['rules'])//这种判断不行 会报错
            if(in_array('rules', $_data)){//如果rules字段存在就将其分割成字符串存入
                $data['rules']=implode(',', $data['rules']);
            }
            //针对管理用户组的权限选择结束
            
            //2、针对用户组启用状态status的选择 开始
            if(!in_array('status', $_data)){
                $data['status']=0;//如果status字段不存在则置0
            }
            //针对用户组启用状态status的选择 结束
            $add=db('auth_group')->insert($data);
            if($add){
                $this->success('添加用户组成功！',url('lst'));
            }else{
                $this->error('添加用户组失败！');
            }
            return;
        }
        $authRule=new \app\admin\model\AuthRule();
        $authRuleRes=$authRule->authRuleTree();
        $admins=db('admin')->find(session('id'));
        $this->assign(array(
            'admin'=>$admins,
            'authRuleRes'=>$authRuleRes,
        ));
        return view();
    }

    public function edit(){
        if(request()->isPost()){
            $data=input('post.');
            //针对复选框的操作
           	//1、针对管理用户组的权限选择开始
            $_data=array();
            foreach ($data as $k => $v) {
                $_data[]=$k;//将页面提交的字段组合成一个一维数组
            }
//          if($data['rules'])//这种判断不行 会报错
            if(in_array('rules', $_data)){//如果rules字段存在就将其分割成字符串存入
                $data['rules']=implode(',', $data['rules']);
            }
            //针对管理用户组的权限选择结束
            
            //2、针对用户组启用状态status的选择 开始
            if(!in_array('status', $_data)){
                $data['status']=0;//如果status字段不存在则置0
            }
            //针对用户组启用状态status的选择 结束
            $save=db('auth_group')->update($data);
            if($save!==false){
                $this->success('修改用户组成功！',url('lst'));
            }else{
                $this->error('修改用户组失败！');
            }
            return;
        }
        //        选择权限        开始 【六维数组】 无限极分类   最多是三级规则
        $data=db('auth_rule')->where(['pid'=>0])->select();//获取顶级权限【二维数组】
        foreach ($data as $k => $v) {
            //$data[$k]['children']赋值后又是一个二维数组 把他看成$data就行
            $data[$k]['children']=db('authRule')->where(['pid'=>$v['id']])->select();//得到二级规则 放到顶级规则下面
            foreach ($data[$k]['children'] as $k1 => $v1) {
                $data[$k]['children'][$k1]['children']=db('authRule')->where(['pid'=>$v1['id']])->select();
            }
        }
//        选择权限         结束
        $id=input('id');
        $authGroups=db('auth_group')->find($id);
        $rules=explode(',',$authGroups['rules']);//用户组已有的权限
        $admins=db('admin')->find(session('id'));//头像用到了
        $this->assign([
            'admin'=>$admins,
            'authGroups'=>$authGroups,
            'data'=>$data,
            'rules'=>$rules,
        ]);
        return view();




//                          第二季采用的
//        $authgroups=db('auth_group')->find(input('id'));
//        $this->assign('authgroups',$authgroups);//用户组已有的权限
//        $authRule=new \app\admin\model\AuthRule();
//        $authRuleRes=$authRule->authRuleTree();
//        $admins=db('admin')->find(session('id'));
//        $this->assign(array(
//            'admin'=>$admins,
//            'authRuleRes'=>$authRuleRes,
//        ));
//        return view();
    }

    public function del(){
        //删除用户组前，先删除该用户组下所有管理员【这里没做 第三季中做了】
        $del=db('auth_group')->delete(input('id'));
        if($del){
            $this->success('删除用户组成功！',url('lst'));
        }else{
            $this->error('删除用户组失败！');
        }
    }
}
