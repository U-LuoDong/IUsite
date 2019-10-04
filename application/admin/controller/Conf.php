<?php
namespace app\admin\controller;
class Conf extends Common
{
    public function conf()
    {
        if(request()->isPost()){
            $data=input('post.');
            $enameArr=db('conf')->column('ename');//查询表中所有的ename
            $confArr=array();//对复选框的处理
            $imgcolumn=db('conf')->where('dt_type',"=",6)->column('ename');//附件类型的字段
            foreach ($data as $k => $v) {//对复选框的处理
                $confArr[]=$k;
                if(is_array($v)){
                    $v=implode(',', $v);//组合成一个字符串
                }
                db('conf')->where('ename',$k)->update(['value'=>$v]);
            }
            //没有这个字段且不是附件类型的字段【是否上传附件都没有对应字段】
            foreach ($enameArr as $k => $v) {
                if(!in_array($v, $confArr) && !in_array($v, $imgcolumn)){
                    db('conf')->where('ename',$v)->update(['value'=>'']);
                }
            }
            //对附件形式进行处理【多图上传】
            foreach ($imgcolumn as $k => $v) {
                //判断是否进行了上传
                if($_FILES[$v]['tmp_name'] != ''){
                    //1、删除服务器中之前的图片
                    $oldImgSrc = db('conf')->field('value')->where('ename',$v)->find();
                    if($oldImgSrc['value']){
                        if (file_exists(ROOT_PATH . 'public/static/admin/uploads/conf/'.$oldImgSrc['value'])) {
                            unlink(ROOT_PATH . 'public/static/admin/uploads/conf/'.$oldImgSrc['value']);
                        }
                    }
                    //2、上传新图
                    $file = request()->file($v);
                    $info = $file->move(ROOT_PATH . 'public/static/admin/uploads/conf');
                    $imgSrc=$info->getSaveName();
                    //                if($imgSrc!=''){//有值才上传，避免出现没有修改图片会出现删除的情况【但不需要 因为前面已经判断了】
                    db('conf')->where('ename',$v)->update(['value'=>$imgSrc]);
                    //                }
                }
            }
            $this->success('修改配置成功！',url('conf'));
            return;
        }
        $confRes=db('conf')->select();
        $admins=db('admin')->find(session('id'));
        $this->assign(array(
            'admin'=>$admins,
            'confRes'=>$confRes,
        ));
        return view();
    }

    public function lst()
    {
        $confRes=db('conf')->field('id,cname,ename,value,values')->paginate(6);
        $admins=db('admin')->find(session('id'));
        $this->assign(array(
            'admin'=>$admins,
            'confRes'=>$confRes,
        ));
        return view();
    }

    public function add()
    {
        if(request()->isPost()){
            $data=input('post.');
            $validate=validate('conf');
            if(!$validate->scene('add')->check($data)){
                $this->error($validate->getError());
            }
            $add=db('conf')->insert($data);
            if($add){
                $this->success('添加配置项成功！',url('lst'));
            }else{
                $this->error('添加配置项失败！');
            }
        }
        $admins=db('admin')->find(session('id'));
        $this->assign('admin',$admins);
        return view();
    }

    public function edit($id)
    {
        if(request()->isPost()){
            $data=input('post.');
            $validate=validate('conf');
            if(!$validate->scene('edit')->check($data)){
                $this->error($validate->getError());
            }
            $save=db('conf')->update($data);
            if($save){
                $this->success('修改配置成功！',url('lst'));
            }else{
                $this->error('修改配置失败！');
            }
        }
        $confs=db('conf')->find($id);
        $admins=db('admin')->find(session('id'));
        $this->assign(array(
            'admin'=>$admins,
            'confs'=>$confs,
        ));
        return view();
    }

    public function del($id){
        $del=db('conf')->delete($id);
        if($del){
            $this->success('删除配置项成功！',url('lst'));
        }else{
            $this->error('删除配置失败！');
        }
    }
}
