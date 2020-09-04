<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\User;
use think\Session;

class Login extends Controller{
	function login(){
		return $this->fetch('index/login');
	}

	function login2($username,$password,$yzm){
		if (!captcha_check($yzm)){
		  return (json(['code' => '-1', 'data' => '', 'msg' => '验证码错误!']));
		}

		$userClass = new User();
		$userRes = $userClass->loginuser($username,$password);

		return $userRes;
	}
}