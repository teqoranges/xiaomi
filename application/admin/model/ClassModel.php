<?php
namespace app\admin\model;
use think\Model;
use think\Db;

//菜单节点
class ClassModel extends Model{

	public function selectAll()
	{
		$selectRes = Db::name('class')->select();
		$return['total'] = 100;
		$return['rows'] = $selectRes;
		return $return;
	}
}