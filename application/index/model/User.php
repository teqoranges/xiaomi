<?php 
namespace app\index\model;	

use think\Model;
use think\Db;
use think\Session;

class User extends Model{
	public function registeruser($username,$password,$tel,$yzm)
	{
		$data = ['username'=>"$username", 'pass'=>"$password", 'tel'=>"$tel"];
		$insertRes = Db::name('user')->insert($data);

		if( $insertRes == 1){
			return json(['code' => 1, 'data' => "login", 'msg' => '注册成功！']);
		}else{
			return json(['code' => -1, 'data' => "", 'msg' => '注册失败！']);
		}
		
	}

	public function loginuser($username,$password)
	{
		$where = [
			'username' => ['eq',"$username",'AND'],
			'pass' => ['eq',"$password"]
		];

		$loginUser = Db::name('user')->where($where)->select();

		if($loginUser != ''){
			session('username', $username);
			session('login_time',date('Y-m-d',time()));    //登录时间  年月日
			return json(['code' => '1' , 'data' => '../index/index','msg' => '登录成功！']);
		}else{
			return json(['code' => '-1' , 'data' => '','msg' => '帐号或者密码错误！']);
		}
		
	}
}
