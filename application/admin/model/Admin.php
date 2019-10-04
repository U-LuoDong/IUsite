<?php

namespace app\admin\model;
use think\Cache as Ca;
use think\Model;

class Admin extends Model
{

    protected static function init()
    {
        //当删除delete的时候触发  删除对应服务器上的图片
        Admin::event('before_delete', function ($txx) {

            $admin = Admin::find($txx->id);
            $txpath = $_SERVER['DOCUMENT_ROOT'] .'/HeadPortrait/' . $admin['HeadPortrait'];
            if (file_exists($txpath)) {
                @unlink($txpath);
            }
        });

    }

    public function addadmin($data)
    {
        if (empty($data) || !is_array($data)) {
            return false;
        }
        if ($data['password']) {
            $data['password'] = md5($data['password']);
        }
        $adminData = array();
        $adminData['name'] = $data['name'];
        $adminData['password'] = $data['password'];
        $adminData['HeadPortrait'] = $data['tx'];
        if ($this->save($adminData)) {
            $groupAccess['uid'] = $this->id;
            $groupAccess['group_id'] = $data['group_id'];
            db('auth_group_access')->insert($groupAccess);//添加管理员的时候还要添加group_access的id和groupid
            return true;
        } else {
            return false;
        }

    }

    public function getadmin()
    {
        return $this::paginate(2);
    }

    public function saveadmin($data, $admins)
    {
        if (!$data['name']) {
            return 2;//管理员用户名为空
        }
        if (!$data['password']) {
            $data['password'] = $admins['password'];
        } else {
            $data['password'] = md5($data['password']);
        }
        db('auth_group_access')->where(array('uid' => $data['id']))->update(['group_id' => $data['group_id']]);
        unset($data['group_id']);//删除group_id字段
        return $this::update($data);
    }

    public function deladmin($id)
    {
        if($id==1){
            return 0;
        }
        if ($this::destroy($id)) {
            //将auth_group_access表中的一起给删除了
            db('auth_group_access')->where(['uid'=>$id])->delete();
            return 1;
        } else {
            return 2;
        }
    }

    public function login($data)
    {
        $admin = Admin::getByName($data['name']);
        if ($admin) {
            if ($admin['password'] == md5($data['password'])) {
                if($admin['status']==0){
                    return 4;//管理员被禁用
                }
                session('id', $admin['id']);
                session('name', $admin['name']);
                return 2; //登录密码正确的情况
            } else {
                return 3; //登录密码错误
            }
        } else {
            return 1; //用户不存在的情况
        }
    }
    /**
     * 判断输入账号是否存在
     */
    public function checkAccount($data){
        $result = $this->where('name',$data['tel'])->find();
        if($result){
            return 1;
        }else{
            return 0;
        }
    }
    /**
     * @param $data
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 通过手机验证码进行登录
     */
    public function getManagerByCode($data){
        $admin = Admin::getByName($data['name']);
        if($admin){
            //如果没有过期就返回就进行对比
            if(session('tel_code')){
                if($data['tel_code']==session('tel_code')){
                    session('id', $admin['id']);
                    session('name', $admin['name']);
                    return 1;
                }else{
                    return -1;//手机验证码不对
                }
            }else{
                return -2;//验证码过期
            }
        }else{
            return 0;//账号不存在
        }
    }
}
