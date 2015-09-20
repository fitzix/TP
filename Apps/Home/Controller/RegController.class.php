<?php
namespace Home\Controller;
use Think\Controller;
class RegController extends Controller{
	public function index(){
		$this -> display();
	}
	
	public function code(){
		$config = array(
			'imageW' => "120px",
			'imageH' => "0",
			"fontSize" => "16",
			"length" => 4,
			"bg" => array(10,200,100)
		);
		$verify = new \Think\Verify($config);
		$verify -> useImgBg = false;
		$verify -> useNoise = false;
		$verify -> entry();
    }
	
	
	//检验注册的用户名是否已存在
	public function check_username(){
		//echo json_encode($_POST['username']);
	    $username = $_POST['username'];
		
		$users = M('users');
		$map['username'] = array('EQ',$username);
		$data = $users -> where($map) -> count();
		//dump($data);
		//exit;
		if($data){
		    $this -> success(1);
		
		}else{
			$this -> error(2);
		
		}
	}
	
	
	//将注册用户信息添加到数据库
	public function insert(){
		//dump($_POST);
		//exit;
		
		$code = I('code');
		$verify = new \Think\Verify();
		$res = $verify -> check($code);
		if(!$res){
			$this -> error("验证码错误！");
			exit;
		}
		
		
		$_POST['password'] = md5($_POST['password']);
		$_POST['password2'] = md5($_POST['password2']);
		
		//信息验证
		$rule = array(
			array('username','require','用户名不能为空'),
			array('password','password2','确认密码不正确！',0,'confirm'),
		
		    array('email','email','邮箱格式不正确！'),
		);	
		
		$users = M('users');
		//dump($_POST);
		//exit;
		if($users -> validate($rule) -> create()){
			if($users -> add()){
					$this -> redirect("login/login");
			}else{
				$this -> error("e！");
				}
		}else{
			$this -> error("a");
		}
	}
}