<?php
namespace app\admin\controller;
use app\admin\model\AuthRule as AuthRuleModel;
use app\admin\controller\Common;
class AuthRule extends Common
{

    public function lst(){
        $authRule=new AuthRuleModel();
        //这里的更新排序需要优化 这里是全部进行更新
        if(request()->isPost()){
            $sorts=input('post.');
            foreach ($sorts as $k => $v) {
                $authRule->update(['id'=>$k,'sort'=>$v]);
            }
            $this->success('更新排序成功！',url('lst'));
            return;
        }
        $authRuleRes=$authRule->authRuleTree();
        $admins=db('admin')->find(session('id'));
        $this->assign(array(
            'admin'=>$admins,
            'authRuleRes'=>$authRuleRes,
        ));
        return view();
    }

    public function add(){
        if(request()->isPost()){
            $data=input('post.');
            $plevel=db('auth_rule')->where('id',$data['pid'])->field('level')->find();//找所属上级的level
            if($plevel){
                $data['level']=$plevel['level']+1;//给新增的赋level
            }else{
            	//上级权限是顶级权限   level为0
               $data['level']=0; 
            }
            $add=db('auth_rule')->insert($data);
            if($add){
                $this->success('添加权限成功！',url('lst'));
            }else{
                $this->error('添加权限失败！');
            }
            return;
        }
        $authRule=new AuthRuleModel();
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
            $plevel=db('auth_rule')->where('id',$data['pid'])->field('level')->find();
            if($plevel){
                $data['level']=$plevel['level']+1;
            }else{
               $data['level']=0; 
            }
            $save=db('auth_rule')->update($data);
            if($save!==false){
                $this->success('修改权限成功！',url('lst'));
            }else{
                $this->error('修改权限失败！');
            }
            return;
        }
        $authRule=new AuthRuleModel();
        $authRuleRes=$authRule->authRuleTree();
        $authRules=$authRule->find(input('id'));
        $admins=db('admin')->find(session('id'));
        $this->assign(array(
            'admin'=>$admins,
            'authRuleRes'=>$authRuleRes,
            'authRules'=>$authRules,
            ));
        return view();
    }


    public function del(){
        $authRule=new AuthRuleModel();
//      $authRule->getparentid(input('id'));//这句话貌似没用
        //删除的时候要将自己所属的一并删除了
        $authRuleIds=$authRule->getchilrenid(input('id'));
        $authRuleIds[]=input('id');//将子类的id和自己的id合并到一起
        $del= AuthRuleModel::destroy($authRuleIds);
        if($del){
            $this->success('删除权限成功！',url('lst'));
        }else{
            $this->error('删除权限失败！');
        }
    }
    /**
     * 权限收缩时将其所有的子栏目全部隐藏【返回所有子权限的id】
     */
    public function ajaxlst()
    {
        if (request()->isAjax()) {
            $authRuleId = input('authRuleId');
            $sonids = model('admin/AuthRule')->getchilrenid($authRuleId);
            echo json_encode($sonids);
        } else {
            $this->error('非法操作！');
        }
    }
}
