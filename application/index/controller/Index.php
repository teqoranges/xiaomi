<?php
namespace app\index\controller;

use think\Controller;
use app\index\controller\Base;
use app\index\model\User;
use think\Session;
use think\Db;
	
class Index extends Controller
{
    public function index()
    {
        $c =Db::table('xiaomi_type');
        $where = [
            'pid' => ['eq','0']
        ];
        $m = Db::table('xiaomi_type')->where($where)->order('id','asc')->select();
        // $type2=array();
        // foreach($m as $key => $value){
        //     $type2 =$c->where("pid=".$value['id'])->select();
        // }

        $this->assign('data',$m);
        return $this->fetch('');
    }

    public function login()
    {
    	return $this->fetch('');
    }

    public function register()
    {
    	return $this->fetch('register');
    }

    public function register2($username,$password,$tel,$yzm)
    {
  //   	if (!captcha_check($yzm)){
		//   return (json(['code' => '-1', 'data' => '', 'msg' => '验证码错误!']));
		// }
        //连接本地的 Redis 服务
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        $yzmTel = "yzm".$tel;
        $rYzm = $redis->get("$yzmTel");
        if($rYzm != ''){
            if($rYzm == $yzm){   
                $userClass = new User();
                $userRes = $userClass->registeruser($username,$password,$tel);

                return $userRes;
            }else{
                return json(['code'=>'-2','data'=>'','msg'=>'验证码错误！']);
            }
        }else{
            return json(['code'=>'-1','data'=>'','msg'=>'验证码已过期！']);
        }
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

    function sendsms()
    {   
        $get = $_GET;
        // dump($get);
        // echo 123;
        // if($get['tel'] != ''){
        //生成随机六位数，不足六位两边补零
        $num = str_pad(mt_rand(0, 999999), 6, "0", STR_PAD_BOTH);

         //连接本地的 Redis 服务
       $redis = new \Redis();
       $redis->connect('127.0.0.1', 6379);
       $yzmTel = "yzm".$get['tel'];
       $redis->set("$yzmTel","$num");
       //echo $redis->get('yzm');
       $redis->expire("yzmTel",60);

        send($get['tel'],$num);
        //}
    }

    function redis()
    {
         //连接本地的 Redis 服务
       $redis = new \Redis();
       $redis->connect('127.0.0.1', 6379);
       //echo $redis->get('yzm');
       // $redis->set('yzm','123456');
       // //echo $redis->get('yzm');
       // $redis->expire('yzm',60);
       // echo $redis->ttl('yzm');
    }
}
