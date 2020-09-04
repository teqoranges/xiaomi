<?php
namespace app\index\controller;

use think\Controller;
use app\index\controller\Base;
use app\index\model\User;
use think\Session;
	
class Index extends Controller
{
    public function index()
    {
        return $this->fetch('');
    }

    public function login()
    {
    	return $this->fetch('');
    }

    public function register()
    {
    	return $this->fetch('');
    }

    public function register2($username,$password,$tel,$yzm)
    {
    	if (!captcha_check($yzm)){
		  return (json(['code' => '-1', 'data' => '', 'msg' => '验证码错误!']));
		}

    	$userClass = new User();
    	$userRes = $userClass->registeruser($username,$password,$tel,$yzm);

    	return $userRes;
    }

    public function login2($username,$password,$yzm)
    {
    	if (!captcha_check($yzm)){
		  return (json(['code' => '-1', 'data' => '', 'msg' => '验证码错误!']));
		}

		$userClass = new User();
		$userRes = $userClass->loginuser($username,$password);

		return $userRes;
    }

    public function zhuxiao()
    {

  		session('username',null);
		session('login_time',null);   //登录时间  年月日

		return $this->redirect(url('login/login'));
    }

    public function gouwuche()
    {
    	 // 1、判断是否登录了
        if (session('username') == null) {
        	echo "<script>
				alert('请您先登录！');
				window.location.href='../login/login';
        	</script>";
        }
        
        //检测登录是过期  一天保质期
        $now = date('Y-m-d',time());
        if($now!=session('login_time')){   //已过期
            echo  "<script>
                alert('登录时间已过期，请重新登录！');
                window.location.href='../login/login';
            </script>";
            //$this->redirect(url('login/index'));
        }

    	return $this->fetch('');
    }

    public function liebiao()
    {
    	return $this->fetch('');
    }

    public function xiangqing()
    {
    	return $this->fetch('');
    }
}
