<?php
namespace app\admin\controller;
use app\admin\model\RoleModel;
use app\admin\model\NodeModel;

/*角色操作*/
class Role extends Base{
    /**
     * 角色列表
     */
    public function index(){
        if(request()->isAjax()){
            
            $param = input('param.');
            
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            
            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['rolename'] = ['like', '%' . $param['searchText'] . '%'];
            }
            $RoleModel = new RoleModel();
            $selectResult = $RoleModel->getRoleByWhere($where, $offset, $limit);
            
            foreach($selectResult as $key=>$vo){
                
                if(1 == $vo['id']){    //超级管理员
                    $selectResult[$key]['operate'] = '';
                    continue;
                }
                
                $operate = [
                    '编辑' => url('role/roleEdit', ['id' => $vo['id']]),
                    '删除' => "javascript:roleDel('".$vo['id']."')",
                    '分配权限' => "javascript:giveQx('".$vo['id']."')"
                ];
                $selectResult[$key]['operate'] = showOperate($operate);
                
            }
            $return['total'] = $RoleModel->getAllRole($where);  //总数据
            $return['rows'] = $selectResult;
            
            return json($return);
        }
        
        return $this->fetch();
        
    }
    
    
    /**
     * 添加角色
     */
    public function roleAdd(){
        if(request()->isPost()){    
            $param=parseParams(input("param.data"));   //转换成数组
            
            $RoleModel = new RoleModel();
            $flag = $RoleModel->insertRole($param);
            
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        
        return $this->fetch("roleadd");
    }
    
    
    
    /**
     * 删除角色
     */
    public function roleDel(){
        $id = input('param.id');
        
        $RoleModel = new RoleModel();
        $flag = $RoleModel->delRole($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
    
    
    //编辑角色
    public function roleEdit(){
        $RoleModel = new RoleModel();
        
        if(request()->isPost()){
            $param = input('post.');
            $param = parseParams($param['data']);    //转换成数组
            $flag = $RoleModel->editRole($param);
            
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        
        $id = input('param.id');
        $this->assign([
            'role' => $RoleModel->getOneRole($id)
        ]);
        return $this->fetch("roleedit");
    }
    
    
    //分配权限
    public function giveAccess(){
        
        $param = input('param.');
        $NodeModel = new NodeModel();
        //获取现在的权限
        if('get' == $param['type']){
            
            $nodeStr = $NodeModel->getNodeInfo($param['id']);
            return json(['code' => 1, 'data' => $nodeStr, 'msg' => 'success']);
        }
        
        
        //分配新权限
        if('give' == $param['type']){
            
            $doparam = [
                'id' => $param['id'],
                'rule' => $param['rule']
            ];
            $RoleModel = new RoleModel();
            $flag = $RoleModel->editAccess($doparam);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
    }
    
   
    
    
    
    
    
    
    
}