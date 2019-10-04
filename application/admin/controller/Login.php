<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Admin;
use think\Cache as Ca;
class Login extends Controller
{
    /**
     * 用账号密码登录
     */
    public function index()
    {
    	if(request()->isPost()){//如果没有提交表单就跳转到登录页面 否则进行登录的验证
//			require '/StudentInformationManagementSystem/denfence.php';//引入安全保护类
			$Defence=new Denfence();//实例化当前的类【在本目录下的不用use引入 】
            //	安全防护开始
            $managerData=array();
			$managerData['name'] = $Defence->clean_xss(input('post.name')); //1.防注入代码   
			$managerData['password'] = $Defence->clean_xss(input('post.password'));
			//	安全防护结束
	   		$user = new Admin();
       		$result=$user->login($managerData);
       		if($result==1){
	   			$this->error("登陆账号不存在，请重新登录...");
       		}else if($result==3){
	   			$this->error("密码不正确，请重新登录...");
       		}else if($result==2){
       		    //保持登录状态时：下次再进入登录界面，会自动跳转到首页【相当于自动登录】
       			if(input('post.category')){
                    session('state','1' );
        			$this->success('登录成功！',url('index/index'));
				}else{
                    session('state','0' );
                    $this->success('登录成功！',url('index/index'));
                }
       		}else if($result == 4){
                $this->error('当前账号已被禁用！');
            }
            return;
        }
    	if(session("state")=='1'){//如果保持登录状态，则直接进入首页
            $this->redirect('Index/index');
//            $this->error('您已经登录成功，请勿重复登录！','Index/index');

//            $admins=db('admin')->find(session('id'));
//            $this->assign('admin',$admins);
//    	    return view('index/index');
        }
//  	return	$this->fetch();
		return  view();//用这个助手函数更加简洁  还不用controller类
   }
    /**
     * 手机验证码登录
     */
    public function useCode()
    {
        if(request()->isPost()){//如果没有提交表单就跳转到登录页面 否则进行登录的验证
            $Defence=new Denfence();//实例化当前的类【在本目录下的不用use引入 】
            //	安全防护开始
            $managerData=array();
            $managerData['name'] = $Defence->clean_xss(input('post.tel')); //1.防注入代码
            $managerData['tel_code'] = $Defence->clean_xss(input('post.tel_code')); //1.防注入代码
            //	安全防护结束
            $user = new Admin();
            $result=$user->getManagerByCode($managerData);
            if($result==0){
                $this->error("登陆账号不存在，请重新登录...");
            }else if($result==-2){
                $this->error("手机验证码已过期，请重新登录...");
            }else if($result==-1){
                $this->error("手机验证码不正确，请重新登录...");
            } else if($result==1){

                //保持登录状态时：下次再进入登录界面，会自动跳转到首页【相当于自动登录】
                if(input('post.category')){
                    session('state','1' );
                    $this->success('登录成功！',url('index/index'));
                }else{
                    session('state','0' );
                    $this->success('登录成功！',url('index/index'));
                }
            }
            return;
        }
        return  view();//用这个助手函数更加简洁  还不要用controller类
    }

    // 验证码检测
    public function check($code='')
    {
        if (!captcha_check($code)) {
            $this->error('验证码错误');
        } else {
            return true;
        }
    }
    /**
     * 发送短信  并返回验证码或错误
     */
    public function sms()
    {
        header("Content-type: text/html; charset=utf-8");
        $phone = input('uPhone_Number');
//        $rand = 8888;
//        $data['result']=1;//测试
        $rand = rand(1000, 9999);
        $url = 'https://api.chanyoo.cn/utf8/interface/send_sms.aspx?username=LuoDong&password=1121luo10086&content=尊敬的用户：'.$phone.'，您正在操作实名认证，验证码：'.$rand.'，请及时提交完成认证，如不是本人操作请忽略！【学生信息后台权限管理系统】&receiver='.$phone;
        $file = file_get_contents($url);
        //转换xml结果
        $xml = simplexml_load_string($file);
        $data = json_decode(json_encode($xml), true);
        if ($data['result'] >= 0) {
            //存入数据库  【不建议】
//			1.发送验证码需要输入图片验证码【防止别人恶意调用】
//			2.验证码还是不要存数据库了，直接存入缓存，两分钟内有效，进行验证
//			3.不建议使用file_get_contents进行验证发送，一般都使用curl
            //存入redis
//            Ca::store('redis')->set($phone, $rand, 120);//两分钟后过期
            session('tel_code',$rand);
//            echo Ca::store('redis')->get($phone)."</br>";
//            session('chtel_code', $rand);
            echo($rand);
        }else{
            echo("0");
        }
    }

    /**
     * 检查账号是否存在
     */
    public function checkAccount(){
        $Defence=new Denfence();//实例化当前的类【在本目录下的不用use引入 】
        //	安全防护开始
        $managerData=array();
        $managerData['tel'] = $Defence->clean_xss(input('post.tel')); //1.防注入代码
        //	安全防护结束
        $user = new Admin();
        $result=$user->checkAccount($managerData);
        if($result==1){
            echo("1");
        }else{
            echo("0");
        }
    }
}
