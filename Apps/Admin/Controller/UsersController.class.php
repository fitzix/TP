<?php
namespace   Admin\Controller;
use Think\Controller;
use Think\Area;
class UsersController extends PublicController{
    public function index(){
		$username = $_GET['username'];
		$state = $_GET['state'];
		if($state == 1 || $state == 2){
			$map['state'] = array('EQ',$state);
			$args['state'] = $state;
		}
		$map['username'] = array('LIKE',"%{$username}%");
		$users = M("users");
        $total = $users -> where($map) -> count();//查询数据总条数
        $args['username'] = $username;
		$Page = new \Think\Page($total,1,$args);//实例化分页类
        $show = $Page -> show();//分页显示输出
		$list = $users ->where($map) -> order('id') -> limit($Page->firstRow.','.$Page->listRows) -> select();
        $this -> assign("users",$list);
        $this -> assign("page",$show);
        $this -> assign("search",$username);
		$this -> display();
    }
    
   //获取修改信息页面
    public function edit(){
        $id = I('id');
		$user = M("users");
		$data = $user -> find($id);
		$arr = explode("#",$data['address']);
		$a = array($arr[0],$arr[1],$arr[2]);
		$city = Area::city($a);
		$data['address'] = $arr[3];
		$this -> assign("city",$city);
        $this -> assign("user",$data);
        $this -> display();
    }
    
	//将修改的会员信息添加到数据库
	public function update(){
        $rule = array(
			array('username','require','用户名不能为空'),
			array('password','password2','确认密码不正确！',0,'confirm'),
			array('password','6,20','密码长度不正确！',0,'length'),
			array('email','email','邮箱格式不正确！'),
		
		);	
	
	    $users = M('users');
		$_POST['address'] = $_POST['province']."#".$_POST['city']."#".$_POST['county']."#".$_POST['address'];
	    $_POST['birthday'] = $_POST['year']."#".$_POST['month']."#".$_POST['day'];
	    if($users ->validate($rule) -> create()){
			if($users -> save()){
				$this -> redirect("Users/index");
			}else{
				$this -> redirect("users/edit");
			}
		}else{
			$this -> error($users -> getError());
		}
    }
	
	//获取添加页面
	public function add(){
        $city = Area::city();
		$this -> assign("city",$city);
		$this -> display();
    }
    
	//添加到数据库
	public function insert(){
        /*
		$rule = array(
			array('username','require','用户名不能为空'),
			array('password2','password','确认密码不正确！',0,'confirm'),
			array('password','6,20','密码长度不正确！',0,'length'),
			array('email','email','邮箱格式不正确！'),
		
		);	
		*/
		$_POST['password'] = md5($_POST['password']);
		$_POST['addtime'] = time();
        $_POST['registerip'] = $_SERVER['REMODE_ADDR'];//注册ip
	    $_POST['address'] = $_POST['province']."#".$_POST['city']."#".$_POST['county']."#".$_POST['address'];
	    $_POST['birthday'] = $_POST['year']."#".$_POST['month']."#".$_POST['day'];
		unset($_POST['password2']);
		$users = M('users');
		if($users ->validate($rule) -> create()){
			if($users -> add()){
				$this -> redirect("users/index");
			}else{
				$this -> redirect("users/add");
			}
		}else{
				$this -> error($users -> getError());
			}
	    }
    
	public function del(){
		$id = I('id');
		$users = M('users');
		if($users -> delete($id)){
			$this -> redirect("users/index");
		}else{
			$this -> redirect("users/index}");
		}
    }
}