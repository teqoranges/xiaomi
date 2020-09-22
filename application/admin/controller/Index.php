<?php 
namespace app\admin\controller;

use app\admin\controller\Base;
use app\admin\model\DashboardModel;
use app\admin\model\ClassModel;
use think\Model;
use think\Db;

class Index extends Base{

    /**
     * 后台默认首页
     * @return mixed
     */
    
    /*显示导航和左侧菜单栏*/
    public function index(){
        return $this->fetch('./index');
    }

    /*在导航和左侧菜单栏中  显示内容页*/
    public function indexPage(){
        return $this->fetch('./index/index');
    }

    public function class(){
        
        if(request()->isajax()){
        $class = new ClassModel;
        $res = $class->selectAll();
        
        return json($res);
        }

        return $this->fetch("./class/class");
    }
   
    public function flview(){
        $m=Db::table('xiaomi_type')->field("*,concat(path,',',id) as paths")->order('paths')->select()->toArray();
        foreach($m as $k => $v){
            $m[$k]['name']=str_repeat("|---",$v['level']).$v['name'];
        }
        //dump($m);
        $this->assign('data',$m);
        return $this->fetch("./fl/fl");
    }

    public function productcategory()
    {
        return $this->fetch("./fl/product-category");
    }

    public function productcategory2()
    {
        $m=Db::table('xiaomi_type')->field('id,pid,name')->select();
        echo json_encode($m);
    }

    public function cpview()
    {
        $m =Db::table("xiaomi_goods")->select();
        $this->assign('data',$m);
        return $this->fetch('./fl/picture-list');
    }

    public function pictureadd()
    {
         $m=Db::table('xiaomi_type')->field("*,concat(path,',',id) as paths")->order('paths')->select()->toArray();
        foreach($m as $k => $v){
            $m[$k]['name']=str_repeat("|---",$v['level']).$v['name'];
        }
        //dump($m);
        $this->assign('data',$m);

        return $this->fetch('./fl/picture-add');
    }

    public function picture_add_goods()
    {
        $post = $_POST;
        $tid = explode(',',$post['pid']);
        
        $data = [
            'goodsname' => $post['goodsname'],
            'tid' => $tid[0],
            'tpid' => $tid[1],
            'unit' => $post['unit'],
            'attributes' => $post['attributes'],
            'imagepath' => "",
            'number' => $post['number'],
            'barcode' => $post['barcode'],
            'curprice' => $post['curprice'],
            'oriprice' => $post['oriprice'],
            'cosprice' => $post['cosprice'],
            'inventory' => $post['inventory'],
            'restrict' => $post['restrict'],
            'already' => $post['already'],
            'freight' => $post['freight'],
            'status' => $post['status'],
            'reorder' => $post['reorder'],
            'text' => $post['text']
        ];

        $db =Db::table('xiaomi_goods')->insert($data);

        if($db == 1){
            return jsom(['code'=>'1','data'=>'','msg'=>'']);
        }
    }

    public function picture_edit()
    {
        $id = $_GET['id'];
        $where = [
            'id' => ['eq',$id]
        ];
        $db =Db::table('xiaomi_goods')->where($where)->find();
        dump($db);
        $this->assign('data',$db);
        return $this->fetch('./fl/picture-edit');
    }

    public function sendsms()
    {
        $get = $_GET;
        if($get['tel'] != '' && $get['data'] != ''){
            send($get['tel'],$get['data']);   
        }
    }
}
