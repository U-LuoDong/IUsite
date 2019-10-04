<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
class Common extends Controller
{
    public $config;//数组 键是英文名称 值是对应的值【缩略图中用到了】

    public function _initialize()
    {
        $this->getConf();//配置 缩略图
        //标志 如果是uploadify的 就不进行登录验证
        if (input('post.mark') != "uploadify") {
            if(!session('id') || !session('name')){
                $this->error('您尚未登录系统','http://www.tp5_2.com/login.html');
            }
        }
        $request = Request::instance();//获取当前的控制器【实现左侧菜单的展开状态】
        $module = $request->module();
        $con = $request->controller();
        $action = $request->action();
        $names = $con.'/'.$action;//下面权限的时候用到了
        $name = $module.'/'.$con.'/'.$action;//组合规则name
        $this->assign(
            [
                //这两个实现左侧菜单的选中样式
                'con' => $con,
                'name' => $name,
            ]
        );
        // auth实现左侧菜单隐藏和权限  开始
        $auth = new Auth();
        $group = $auth->getGroups(session('id'));//利用auth自带的函数 根据用户id查找对应规则
        $rules = explode(',', $group[0]['rules']);//存放规则id的数组

        //使用连表查询代替getGroups函数
//        $group = db('auth_group_access')->alias('a')->field('g.rules')
//            ->join('auth_group g', 'a.group_id=g.id')
//            ->where(['a.uid' => session('id')])
//            ->find();
//        $rules = explode(',', $group['rules']);//存放规则id的数组


        $menu = array();
        $map['pid'] = ['=', 0];//$map['pid']=0;功能是一样的
        $map['show'] = ['=', 1];//show=1【是否在左侧菜单显示，主要解决edit、del需要传递id的问题】
        $map['id'] = ['in', $rules];
//      dump($map);die;
        $_map['id'] = ['in', $rules];
        $menu = db('authRule')->where($map)->order('sort asc')->select();//查找顶级规则
        //逻辑和authgroup的power方法差不多 区别是这里的规则是该管理员拥有的【多了限定条件】
        foreach ($menu as $k => $v) {
            $menu[$k]['children'] = db('authRule')->where($_map)->where(array('pid' => $v['id'], 'show' => 1))->select(
            );
            foreach ($menu[$k]['children'] as $k1 => $v1) {
                $menu[$k]['children'][$k1]['children'] = db('authRule')->where($_map)->where(
                    array('pid' => $v1['id'], 'show' => 1)
                )->select();
            }
        }
        // print_r($menu); die;
        $this->assign(
            [
                'menu' => $menu,//存放属于该管理员的所有权限 排序好的
            ]
        );
        //可以访问首页、退出登录、修改密码
        $notCheck=array('Index/index','Admin/lst','Admin/logout','Admin/edit');//不论那个管理员都可以访问的页面
//        if(session('id')!=1){//针对超级管理员 如果id为1就不需要验证
            if (!in_array($names, $notCheck)) {
                //调用auth里面封装好的方法来判断是否有权限
                if (!$auth->check($name, session('id'))) {
                    $this->error('没有权限', url('Index/index'));
                }
            }
//         }
        // auth实现左侧菜单隐藏和权限  结束
//      dump($this->config); die;
    }
    /**
     * 获取配置中的数据
     */
    public function getConf()
    {
        $confRes = array();//键是英文名称 值是对应的值
        $_confRes = db('conf')->field('ename,value')->select();
        foreach ($_confRes as $v) {
            $confRes[$v['ename']] = $v['value'];
        }
        $this->config = $confRes;
    }

}
