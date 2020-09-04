<?php
namespace app\admin\controller;
use app\admin\model\UserModel;
use app\admin\model\RoleModel;
use app\admin\model\LoginlogModel;



/*管理员操作*/

class User extends Base{
    /**
     * 用户列表
     */
    public function index(){
        if(request()->isAjax()){
            //1、接收参数
            $param = input('param.');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;   
            
            //2、搜索条件
            $where=[];     
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['username'] = ['like', '%' . $param['searchText'] . '%'];
            }
            
            if(isset($param['is_leave']) && !empty($param['is_leave'])){
                $where['is_leave'] = $param['is_leave']; 
            }
            
            if(isset($param['typeid']) && !empty($param['typeid'])){
                $where['typeid'] = $param['typeid'];
            }
            
            //3、查找数据
            $user = new UserModel();
            $selectResult = $user->getUsersByWhere($where, $offset, $limit);
  
            $status = config('user_status');
            $is_leave = config('user_leave');
            
            //4、修改时间，状态，操作
            foreach($selectResult as $key=>$vo){
                
                $selectResult[$key]['last_login_time'] = date('Y-m-d H:i:s', $vo['last_login_time']);
                $selectResult[$key]['status'] = $status[$vo['status']];
                $selectResult[$key]['is_leave'] = $is_leave[$vo['is_leave']];
                
                $sql = "SELECT real_name FROM admin_user WHERE id = " . $vo['pid'];
                $pid_name = $user->query($sql);
                $selectResult[$key]['p_real_name'] = $pid_name[0]['real_name'];       //上级
                
                //操作按钮生成
                $operate = [
                    '编辑' => url('user/userEdit', ['id' => $vo['id']]),
                    '删除' => "javascript:userDel('".$vo['id']."')"
                ];
                
                $selectResult[$key]['operate'] = showOperate($operate);
                
                if( 1 == $vo['id'] ){    //超级管理员
                    $selectResult[$key]['operate'] = '';
                }
            }

            //5、分配数据到bootstrap表格里面去
            $return['total'] = $user->getAllUsers($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }
        
        $RoleModel = new RoleModel();
        $this->assign([
            'role' => $RoleModel->getRole(),
            'is_leave' => config('user_leave')
        ]);
        return $this->fetch();   
    }
    
    
    /**
     * 添加管理员页面
     */
    public function userAdd(){
        if(request()->isAjax()){
            $params=parseParams(input("param.data"));   //转换成数组
            $params['password']=md5($params['password']);
            
            if(!empty($params)){
                $UserModel=new UserModel();
                $flag=$UserModel->insertUser($params);
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
        }
        $RoleModel = new RoleModel();
        $tiaojian['id'] = ['neq',1];    //获取不是超级管理员的所有角色
        $this->assign([
            'role' => $RoleModel->getRole($tiaojian),
            'is_leave' => config('user_leave'),
            'status' => config('user_status')
        ]);
        return $this->fetch("useradd");
        
    }
    
    /**
     * 删除管理员
     */
    public function userDel(){
        $id = input('param.id');
        
        $UserModel = new UserModel();
        $flag = $UserModel->delUser($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    
    
    
    
    /**
     * 编辑管理员
     */
    public function userEdit(){
        $UserModel = new UserModel();
        
        //修改数据
        if(request()->isPost()){
            
            $param = input('post.');
            $param = parseParams($param['data']);
            if(empty($param['password'])){
                unset($param['password']);
            }else{
                $param['password'] = md5($param['password']);
            }
            $flag = $UserModel->editUser($param);
            
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        
        
        //数据回显
        $id = input('param.id');
        $RoleModel = new RoleModel();
        $tiaojian['id'] = ['neq',1];    //获取不是超级管理员的所有角色
        $this->assign([
            'user' => $UserModel->getOneUser($id),
            'userList' => $UserModel->getAllUser(),
            'status' => config('user_status'),
            'role' => $RoleModel->getRole($tiaojian),
            'is_leave' => config('user_leave')
        ]);
        return $this->fetch("useredit");
        
    }
    

    
    
    
    
    
    
}