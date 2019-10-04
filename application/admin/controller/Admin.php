<?php
namespace app\admin\controller;
use app\admin\model\Admin as AdminModel;
use app\admin\controller\Common;
class Admin extends Common
{
    public function lst()
    {   
    	$auth=new Auth();//实例化当前的类【在本目录下的不用use引入 】
        $admin=new AdminModel();
        $adminres=$admin->getadmin();
        foreach ($adminres as $k => $v) {
            $_groupTitle=$auth->getGroups($v['id']);//getGroups返回的是一个二维数组
            $groupTitle=$_groupTitle[0]['title'];
            $v['groupTitle']=$groupTitle;
        }
        $admins=db('admin')->find(session('id'));
        $this->assign(array(
            'admin'=>$admins,
            'adminres'=>$adminres,
        ));
        return view();
	}

	public function add()
    {
        if(request()->isPost()){
            $data=input('post.');
            $validate = \think\Loader::validate('Admin');
            if(!$validate->scene('add')->check($data)){
                $this->error($validate->getError());
            }
            $admin=new AdminModel();
            if($admin->addadmin($data)){
                $this->success('添加管理员成功！',url('lst'));
            }else{
                $this->error('添加管理员失败！');
            }
            return;
        }
        $authGroupRes=db('auth_group')->select();
        $admins=db('admin')->find(session('id'));
        $this->assign(array(
            'admin'=>$admins,
            'authGroupRes'=>$authGroupRes,
        ));
        return view();
	}

	public function edit($id)
    {
    	//最好用input('get.id')来获取
        $admins1=db('admin')->find($id);
        if(request()->isPost()){
            $data=input('post.');
            $validate = \think\Loader::validate('Admin');
            if(!$validate->scene('edit')->check($data)){
                $this->error($validate->getError());
            }
            $admin=new AdminModel();
            $savenum=$admin->saveadmin($data,$admins1);//第三季的方法比较好
            if($savenum == '2'){
                $this->error('管理员用户名不得为空！');
            }
            if($savenum !== false){
                $this->success('修改成功！',url('lst'));
            }else{
                $this->error('修改失败！');
            }
            return;
        }
        
        if(!$admins1){
            $this->error('该管理员不存在');
        }
        $authGroupAccess=db('auth_group_access')->where(array('uid'=>$id))->find();
        $authGroupRes=db('auth_group')->select();
        $this->assign('authGroupRes',$authGroupRes);
        $admins=db('admin')->find(session('id'));//注意区分，一个是当前登录的用户，一个是要进行修改的用户
        $this->assign(array(
            'admin'=>$admins,
            'admin1'=>$admins1,
        ));
        $this->assign('groupId',$authGroupAccess['group_id']);
        return view();
	}

    public function del($id){
        $admin=new AdminModel();
        $delnum=$admin->deladmin($id);
        if($delnum == '1'){
            $this->success('删除管理员成功！',url('lst'));
        }elseif ($delnum == '0'){
            $this->error('超级管理员不允许删除！');
        }else{
            $this->error('删除管理员失败！');
        }
    }

    public function logout(){
        session(null); 
        $this->success('退出系统成功！','http://www.tp5_2.com/login.html');
    }

    /**
     * uploadify异步上传图片
     */
    public function upimg()
    {
        if ($_FILES['tx']['tmp_name']) {
            $adminId = input('adminId');
            $file = request()->file('tx');
            $info = $file->move(ROOT_PATH.'public/HeadPortrait');
            //1、缩略图
            if ($this->config['thumb'] == '是') {
                $imgSrc = ROOT_PATH.'public/HeadPortrait/'.($info->getSaveName());//原图位置
                $image = \think\Image::open($imgSrc);
                $width = $this->config['thumb_width'];
                $height = $this->config['thumb_height'];
                $thumb_way = $this->config['thumb_way'];
                switch ($thumb_way) {
                    case '等比例缩放':
                        $thumb_way = 1;
                        break;
                    case '缩放后填充':
                        $thumb_way = 2;
                        break;
                    case '居中裁剪':
                        $thumb_way = 3;
                        break;
                    case '左上角裁剪':
                        $thumb_way = 4;
                        break;
                    case '右下角裁剪':
                        $thumb_way = 5;
                        break;
                    case '固定尺寸缩放':
                        $thumb_way = 6;
                        break;
                    default:
                        $thumb_way = 1;
                        break;
                }
                //2、水印
                if ($this->config['water'] == '是') {
                    $waterImg = ROOT_PATH . 'public/static/admin/uploads/conf/'.$this->config['water_img'];
                    $water_pos = $this->config['water_pos'];
                    switch ($water_pos) {
                        case '左上角':
                            $water_pos = 1;
                            break;
                        case '上居中':
                            $water_pos = 2;
                            break;
                        case '右上角':
                            $water_pos = 3;
                            break;
                        case '左居中':
                            $water_pos = 4;
                            break;
                        case '居中':
                            $water_pos = 5;
                            break;
                        case '右居中':
                            $water_pos = 6;
                            break;
                        case '左下角':
                            $water_pos = 7;
                            break;
                        case '下居中':
                            $water_pos = 8;
                            break;
                        case '右下角':
                            $water_pos = 9;
                            break;
                        default:
                            $water_pos = 9;
                            break;
                    }
                    $tmd = $this->config['tmd'];
                    $image->thumb($width, $height, $thumb_way)->water($waterImg, $water_pos, $tmd)->save($imgSrc);
                } else {
                    $image->thumb($width, $height, $thumb_way)->save($imgSrc);
                }
            }
            if ($info) {
                //针对修改
                if ($adminId) {
                    db('admin')->where(['id' => $adminId])->update(['HeadPortrait' => $info->getSaveName()]);
                }
                echo $info->getSaveName();
            } else {
                echo $file->getError();
            }
        }





//        if ($_FILES['tx']['tmp_name']) {
//            $adminId = input('adminId');
//            $file = request()->file('tx');
//            $info = $file->move(ROOT_PATH.'public/HeadPortrait');
//            if ($info) {
//                // 成功上传后 获取上传信息
//                if ($adminId) {//【针对修改界面 添加界面没有cateid】
//                    db('admin')->where('id', $adminId)->setField('HeadPortrait', $info->getSaveName());
//                }
//                echo $info->getSaveName();
//            } else {
//                // 上传失败获取错误信息
//                echo $file->getError();
//            }
//        }
    }

    /**
     * 异步撤销图片
     */
    public function delimg()
    {
        //1、修改表记录 针对修改edit
        $adminId = input('adminId');
        if ($adminId) {
            db('admin')->where(['id' => $adminId])->update(['HeadPortrait' => '']);
        }
        //2、删除服务器中的图片
        if (file_exists(TXIMG.input('imgurl'))) {
            $del = unlink(TXIMG.input('imgurl'));
        }
        if ($del) {
            echo 1;
        } else {
            echo 0;
        }
    }
    /**
     * 异步修改管理员状态
     */
    public function changestatus(){
        $aid = input('post.id');
        $admins = db('admin')->find($aid);
        if($admins['status']==1){
            db('admin')->where(['id'=>$aid])->update(['status'=>0]);
            echo "1";
        }else{
            db('admin')->where(['id'=>$aid])->update(['status'=>1]);
            echo "0";
        }
    }
}
