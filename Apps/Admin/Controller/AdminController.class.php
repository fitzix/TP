<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Area;
class AdminController extends Controller{
	//获取员工列表
	
	 public function index(){
        $admin = M("admin");
	    $name = $_GET['name'];
		$map['name'] = array("LIKE","%{$name}%");
		$state = $_GET['state'];
		if($state){
			$map['state'] = array('EQ',$state);
			$args['state'] = $state;
		}
		$count = $admin ->where($map) -> count();
        $args['name'] = $name;
		$Page = new \Think\Page($count,1,$args);
		$show = $Page -> show();
        $list = $admin ->where($map) ->order("addtime") -> limit($Page->firstRow .','.$Page->listRows) -> select();
        $this -> assign("page",$show);
        $this -> assign("list",$list);
        $this -> assign("search",$name);
        $this -> display();
    }

	//获取添加员工页面
	public function add(){
		$city = Area::city();
		$this -> assign("city",$city);
		$this -> display();
		
	}
	
	//将员工信息添加到数据库
	public function insert(){
		/*
		$rule = array(
			array('name','require','用户名不能为空'),
			array('password','password2','确认密码不正确！',0,'confirm'),
			array('password','6,32','密码长度不正确！',0,'length'),
			array('email','email','邮箱格式不正确！'),
		
		);	
		*/
		
		$_POST['address'] = $_POST['province']."#".$_POST['city']."#".$_POST['county']."#".$_POST['address'];
        unset($_POST['password2']);
		$_POST['addtime'] = time();
		$_POST['password'] = md5($_POST['password']);
		//$_POST['password2'] = md5($_POST['password2']);
		$admin = M('admin');
		//dump($_POST);
		//exit;
		if($admin ->validate($rule) -> create()){
			if($admin -> add()){
				$this -> redirect('admin/index');
			}else{
				$this -> redirect('admin/add');
			}
		}else{
			$this -> error($admin -> getError());
		}
	}
	
	//获取修改页面
	public function edit(){
		$id = I('id');
		$admin = M('admin');
		$data = $admin -> find($id);
		$address =  $data['address'];
		$arr = explode("#",$address);
		$data['address'] = $arr[3];
		$a = array($arr[0],$arr[1],$arr[2]);
		$city = Area::city($a);
		$this -> assign("city",$city);
		$this -> assign('admin',$data);
		$this -> display();
	}
	
	//将修改的信息保存到数据库
	public function update(){
		$rule = array(
			array('name','require','用户名不能为空'),
			array('password','password2','确认密码不正确！',0,'confirm'),
			array('password','6,20','密码长度不正确！',0,'length'),
			array('email','email','邮箱格式不正确！'),
		
		);	
		
		$admin = M('admin');
		$_POST['address'] = $_POST['province']."#".$_POST['city']."#".$_POST['county']."#".$_POST['address'];
	    if($admin ->validate($rule) -> create()){
			if($admin -> save()){
				$this -> redirect("admin/index");
			}else{
				$this -> redirect("admin/index");
			}
		}else{
			$this -> error($admin -> getError());
		}
	}
	
	//删除员工
	public function del(){
		$id = I('id');
		$admin = M('admin');
		if($admin -> delete($id)){
			$this -> redirect("admin/index");
		}else{
			$this -> redirect("admin/index");
		}
	}
	
	//获取修改密码页面
	public function editpwd(){
		$this -> display();
	}
	//
	public function updatepwd(){
		$data['id'] = $_POST['id'];
        $data['password'] = md5($_POST['password']);
		$admin = M("admin");
		if($admin -> create($data)){
			if($admin -> save()){
				$this -> success("修改成功！",U('Admin/index'));
			}else{
				$this -> error("修改失败！");
			}
		}else{
			$this -> error("修改失败！");
		}
	}
	
	
	
	
	
	
	
	
	
	//获取员工设置组页面
	public function editgroup(){
		//获取管理员id
		$id = $_GET['id'];
		//根据 uid 查询所属组 group_id
		$auth_group_access = M('auth_group_access');
		$auth_group = M('auth_group');
		$d = $auth_group_access -> where("uid={$id}") -> select();
		//dump($d);
		//exit;
		foreach($d as $k=>$v){
			//echo $v['group_id'];
			$group[$k] = $auth_group -> find($v['group_id']);
		}
		$data = $auth_group -> select();
		$this -> assign('data',$data);
		$this -> assign('group',$group);
		$this -> display();
	}
	
	public function updategroup(){
		$auth_group_access = M('auth_group_access');
	    $data['uid'] = $_POST['uid'];
		//根据 uid 查询是否有所属组，如果有则输出所有所属组
		if($auth_group_access -> where("uid={$data['uid']}") -> select()){
		    //根据uid删除原有的所属组
			$auth_group_access ->where("uid={$data['uid']}") -> delete();
		}
		//遍历所有 group_id,并加入数据库
		foreach($_POST['group_id'] as $group_id){
			$data['group_id'] =  $group_id;
			if($auth_group_access -> create($data)){
				$auth_group_access -> add();
			}	
		}
		$this -> redirect('admin/index');
	}	
}