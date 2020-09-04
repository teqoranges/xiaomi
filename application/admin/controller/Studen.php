<?php 
namespace app\admin\controller;

use app\admin\controller\Base;
use app\admin\model\ClassModel;

class Studen extends Base{

    /**
     * 后台默认首页
     * @return mixed
     */
    
    public function class(){
        return $this->fetch("./class/class");

        // $class = new ClassModel;
        // $res = $class->selectAll();
        // dump($res);
    }

   
}
