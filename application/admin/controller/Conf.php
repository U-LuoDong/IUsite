<?php
namespace app\admin\controller;
use app\admin\model\Conf as ConfModel;
use app\admin\controller\Common;
class Conf extends Common
{
    public function lst(){
        if(request()->isPost()){
            $sorts=input('post.');
            $conf=new ConfModel();
            foreach ($sorts as $k => $v) {
                $conf->update(['id'=>$k,'sort'=>$v]);
            }
            $this->success('更新排序成功！',url('lst'));
            return;
        }
        $confres=ConfModel::order('sort desc')->paginate(4);
        $admins=db('admin')->find(session('id'));
        $this->assign(array(
            'admin'=>$admins,
            'confres'=>$confres,
        ));
        return view();
    }

    public function add(){
        if(request()->isPost()){
            $data=input('post.');
            $validate = \think\Loader::validate('Conf');
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }
            if($data['values']){
                $data['values']=str_replace('，', ',', $data['values']);
            }
            $conf=new ConfModel();
            if($conf->save($data)){
                $this->success('添加配置成功！',url('lst'));
            }else{
                $this->error('添加配置失败！');
            }
        }
        $admins=db('admin')->find(session('id'));
        $this->assign('admin',$admins);
        return view();
    }

    public function edit(){
        if(request()->isPost()){
            $data=input('post.');
            $validate = \think\Loader::validate('Conf');
            if(!$validate->scene('edit')->check($data)){
                $this->error($validate->getError());
            }
            if($data['values']){
                $data['values']=str_replace('，', ',', $data['values']);
            }
            $conf=new ConfModel();
            $save=$conf->save($data,['id'=>$data['id']]);
            if($save!==false){
                $this->success('修改配置成功！',url('lst'));
            }else{
                $this->error('修改配置失败！');
            }
        }
        $confs=ConfModel::find(input('id'));
        $admins=db('admin')->find(session('id'));
        $this->assign(array(
            'admin'=>$admins,
            'confs'=>$confs,
        ));
        return view();
    }

    public function del(){
        $del=ConfModel::destroy(input('id'));
        if($del){
           $this->success('删除配置项成功！',url('lst')); 
        }else{
            $this->error('删除配置项失败！');
        }
    }

    public function conf(){
        if(request()->isPost()){
            $data=input('post.');
            $formarr=array();//1、将页面提交过来的数组整合成字段数组
            foreach ($data as $k => $v) {
                $formarr[]=$k;
            }
            $_confarr=db('conf')->field('enname')->select();
            $confarr=array();//2、将查询出来的二维数组转换成一维字段数组
            foreach ($_confarr as $k => $v) {
                $confarr[]=$v['enname'];
            }
            $checkboxarr=array();
            foreach ($confarr as $k => $v) {
                if(!in_array($v, $formarr)){//3、判断数据表的字段在网页提交中的是否存在
                    $checkboxarr[]=$v;//4、将不存在的字段整合成一个数组
                }
            }
            if($checkboxarr){//5、如果有值就将对应字段赋空值
                foreach ($checkboxarr as $ke => $v) {
                    ConfModel::where('enname',$v)->update(['value'=>'']);
                }
            }
            if($data){//6、再更新提交的值
                foreach ($data as $k=>$v) {
                    ConfModel::where('enname',$k)->update(['value'=>$v]);
                }
                $this->success('修改配置成功！');
            }
            return;
        }
        $confres=ConfModel::order('sort desc')->select();
        $admins=db('admin')->find(session('id'));
        $this->assign(array(
            'admin'=>$admins,
            'confres'=>$confres,
        ));
        return view();
    }





    

    




   

	












}
