<?php
namespace app\admin\controller;
use app\admin\model\Cate as CateModel;
use app\admin\model\Article as ArticleModel;
use app\admin\controller\Common;
class cate extends Common
{

//     前置方法
//    protected $beforeActionList = [
//        // 'first',
//        // 'second' =>  ['except'=>'hello'],
//        'delsoncate'  =>  ['only'=>'del'],
//    ];


    public function lst()
    {
        $cate=new CateModel();
        if(request()->isPost()){
            $sorts=input('post.');
            foreach ($sorts as $k => $v) {
                $cate->update(['id'=>$k,'sort'=>$v]);
            }
            $this->success('更新排序成功！',url('lst'));
            return;
        }
        $cateres=$cate->catetree();
        $admins=db('admin')->find(session('id'));
        $this->assign(array(
            'admin'=>$admins,
            'cateres'=>$cateres,
        ));
        return view();
	}

    public function add(){
        $cate=new CateModel();
        if(request()->isPost()){
            $data=input('post.');
            $validate = \think\Loader::validate('Cate');
            if(!$validate->scene('add')->check($data)){
                $this->error($validate->getError());
            }
           $add=$cate->save($data);
           if($add){
                $this->success('添加栏目成功！',url('lst'));
           }else{
                $this->error('添加栏目失败！');
           }
        }
        $cateres=$cate->catetree();
        $admins=db('admin')->find(session('id'));
        $this->assign(array(
            'admin'=>$admins,
            'cateres'=>$cateres,
        ));
        return view();
    }

    public function edit(){
        $cate=new CateModel();
        if(request()->isPost()){
            $data=input('post.');
            $validate = \think\Loader::validate('Cate');
            if(!$validate->scene('edit')->check($data)){
                $this->error($validate->getError());
            }
            $save=$cate->save($data,['id'=>$data['id']]);
            if($save !== false){
                $this->success('修改栏目成功！',url('lst'));
            }else{
                $this->error('修改栏目失败！');
            }
            return;
        }
        $cates=$cate->find(input('id'));
        $cateres=$cate->catetree();
        $admins=db('admin')->find(session('id'));
        $this->assign(array(
            'admin'=>$admins,
            'cateres'=>$cateres,
            'cates'=>$cates,
            ));
        return view();
    }
    /**
     * 1、栏目、子栏目的内容 2、所有栏目对应的文章内容和缩略图【共三个】
     */
    public function del(){
        $cateIds=model('Cate')->getchilrenid(input('id'));
        $cateIds[]=(int)input('id');
        foreach ($cateIds as $key=>$v){
            $articles=db('article')->where('cateid','=',$v)->select();
            foreach ($articles as $v1){
                //1、删除文章的缩略图
                if($v1['thumb']){
                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $v1['thumb'])){
                        unlink($_SERVER['DOCUMENT_ROOT'] . $v1['thumb']);
                    }
                }
                //2、删除文章内容
                db('article')->delete($v1['id']);
            }
        }
        //3、删除所有栏目的内容
        $del = db('cate')->delete($cateIds);
        if($del){
            $this->success('删除栏目成功！','lst');
        }else{
            $this->error('删除栏目失败!');
        }
    }
    /**
     * 栏目收缩时将其所有的子栏目全部隐藏【返回所有子栏目的id】
     */
    public function ajaxlst()
    {
        if (request()->isAjax()) {
            $cateid = input('cateid');
            $sonids = model('cate')->getchilrenid($cateid);
            echo json_encode($sonids);
        } else {
            $this->error('非法操作！');
        }
    }
}
