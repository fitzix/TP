<?php
namespace Admin\Controller;
use Think\Controller;

class AclController extends Controller{
	//设置权限页面
	public function setAcl(){
		$this -> display();
	}
	
	//将权限数据添加到数据库
	public function insert(){
		$auth_rule = M("auth_rule");
        //dump($_POST);
		foreach($_POST as $k => $v){
			$data['name'] = $k;
			$data['title'] = $v;
			//dump($data);
			//exit;
			if($auth_rule -> create($data)){
				$auth_rule -> add();
			}
		}
		$this -> redirect("acl/setAcl");
	}
}