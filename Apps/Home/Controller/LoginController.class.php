<?php
namespace Home\Controller;
use Think\Controller;

class LoginController extends Controller{
	public function login(){
		$this -> display();
	}
	
	public function code(){
        $config = array(
            'imageW' => "120px",
            'imageH' => "0",
            "fontSize" => "16",
            "length" => 4
        );
        $verify = new \Think\Verify($config);
        $verify -> useImgBg = false;
        $verify -> useNoise = false;
        $verify -> entry();
    }
	
	public function prologin(){
		$code = I('code');
		$verify = new \Think\Verify();
		$res = $verify -> check($code);
		if(!$res){
			$this -> error('验证码错误！');
			exit;
		}
		
		if(!$_POST['password']){
		   $this -> error("请输入密码！");
			exit;
		}
		$users = M("users");
	    $map['username'] = array("EQ",$_POST['username']);
		$data = $users -> where($map) -> find();
		//dump($data['password']);
		//dump(md5($_POST['password']));
		//exit;
		if($data){
			if($data['password'] == md5($_POST['password'])){
				//登录成功！将用户 'id'  /  'username' 信息存入到session 中
				session("uid",$data['id']);
				session('username',$data['username']);
				$this -> redirect('index/index');
				//echo "成功！";
				
			}else{
				$this -> error("密码不正确！");
				exit;
			}
		}else{
			$this -> error("账号不存在！");
			exit;
		}
	}
	
	public function logout(){
		session('username',null);
		session('uid',null);
		$this -> redirect('/Login/login');
	}
	
	//================================找回密码====================================
	public function forget_pwd(){
		
		$this -> display();
	}
	
	
	public function reset_pwd(){
		$email = $_POST['email'];
		$users = M('users');
		$map['email'] = array('EQ',$email);
		$data = $users -> where($map) -> find();
		
		if($data){
			//dump($data);
			//exit;
			$this -> assign("data",$data);
			$this -> display();
		}else{
			$this -> error("邮箱不存在！");
		}
	}
	
	public function save_reset_pwd(){
		//dump($_POST);
		//exit;
		if($_POST['password']="" || $_POST['password2']=""){
			$this -> error("密码不能为空");
		}
		if($_POST['password']==$_POST['password2']){
			 $data['password'] = md5($_POST['password']);
			 $data['id'] = $_POST['id'];
		   //  dump($data);
			$users = M("users");
			if($users -> create($data)){
				if($users -> save()){
					$this -> success("修改成功",U('login/login'));
				}else{
					$this -> error("失败！");
				}
			}else{
				$this -> error(2);
			}
		}else{
			$this -> error("输入的密码不一致！");
		}
		
	}
} 

