<?php
namespace Admin\Controller;
use Think\Controller;
class AdminGroupController extends Controller{
	public function addGroup(){
		$auth_rule = M("auth_rule");
		for($i=1; $i <=14; $i++){
			$map["sort"] = array("EQ",$i);
			$data[$i] = $auth_rule -> where($map) -> select();
		}
		//dump($data);
		$this -> assign("data",$data);
		$this -> display();
		
	}
	
	public function insert(){
		//dump($_POST);
		//exit;
		$data["title"] = $_POST['title'];
		$data["rules"] = implode(',',$_POST['rules']) ;
		//dump($data['rules']);
		//exit;
		$auth_group = M('auth_group');
		if($auth_group -> create($data)){
			if($auth_group -> add()){
				$this -> success("添加成功！");
				//$this -> redirect('admingroup\addgroup');
			}else{
				$this -> error("添加失败！");
				//$this -> redirect('admingroup\addgroup');
			}
		}
	}
	
	//================浏览权限组====================
	public function index(){
		//$title = I('title');
		//$map['title'] = array('LIKE',$title);
		//$map['status'] = array('EQ',1);
		//dump($map);
		$auth_group = M('auth_group');
		$count = $auth_group ->where() -> count();
		$Page = new \Think\Page($count,5);
		$show = $Page -> show();
		$list = $auth_group ->where() -> limit($Page->firstRow.','.$Page->listRows) -> select();
		$this -> assign('data',$list);
		$this -> assign('page',$show);
		$this -> display();
		
	}
	
	//================删除用户组=====================
	public function del_auth_group(){
		$id = I('id');
		$auth_group = M('auth_group');
		if($auth_group -> delete($id)){
			$this -> success("删除成功!");
		}else{
			$this -> error("删除失败！");
		}
	}
	
	//==================查看详细权限=================
	
	public function check_all_rules(){
		$rules = explode(",",I('rules'));
		$len = count($rules);
	    $auth_rule = M('auth_rule');
		for($i=0 ;$i < $len ;$i++){
			$map['id'] = array('EQ',$rules[$i]);
			$data[$i] = $auth_rule -> where($map) -> find();
		}
		$this -> ajaxReturn($data);
	}
	
	
	
	
	
	
}

